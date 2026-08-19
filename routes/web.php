<?php

/*
|--------------------------------------------------------------------------
| Web Routes  —  Public / Frontend only
|--------------------------------------------------------------------------
| Admin routes  →  routes/admin.php  (prefix: /crm)
| User auth     →  routes/user.php   (prefix: /login, /register, /dashboard)
*/

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageEnquiryController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// ── Public frontend routes ───────────────────────────────────────────────
// Every route below goes down together when Super Admin blocks this company
// (App\Http\Middleware\EnsureSiteNotBlocked, tenant resolved from config —
// see App\Support\SiteTenant). The /crm admin panel is NOT in this file; it
// resolves its tenant from the authenticated admin instead (see routes/admin.php).
Route::middleware('site.blocked')->group(function () {

Route::get('/',            [DashboardController::class, 'welcome'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt',  [RobotsController::class, 'index'])->name('robots');
Route::post('/submit-query', [DashboardController::class, 'submitQuery'])->name('submitquery');
Route::get('/get-cities/{state}', [DashboardController::class, 'getCities'])->name('getcities');
// Rich About Page module (Hero/Story/Team/Stats/etc., edited at /crm/about-page).
Route::get('/about-us', [AboutController::class, 'show'])->name('about-us');
Route::get('/contact-us',  [ContactController::class, 'show'])->name('contact_us');
Route::get('/coming-soon/{product}', [ComingSoonController::class, 'show'])->name('coming.soon');
Route::post('/submit-contact-form', [DashboardController::class, 'submitContactForm'])->name('submit_contact_form');

// CRM product landing page — business enquiries (not the /crm admin panel, see routes/admin.php)
Route::get('/crm',          [CrmController::class, 'landing'])->name('crm.landing');
Route::post('/crm/enquiry', [CrmController::class, 'storeEnquiry'])->name('crm.enquiry.store');

// ── Navbar destinations: package listings (share one design) + detail page ──
Route::get('/india-packages',         [PackageController::class, 'india'])->name('packages.india');
Route::get('/international-packages', [PackageController::class, 'international'])->name('packages.international');
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{destination:slug}/packages', [PackageController::class, 'byDestination'])->name('packages.byDestination');
Route::get('/destinations/{destination:slug}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/mice',                   [PackageController::class, 'mice'])->name('packages.mice');
Route::get('/blog',                   [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog:slug}',       [BlogController::class, 'show'])->name('blog.show');
Route::get('/holiday-packages',       [PackageController::class, 'all'])->name('packages.index');
Route::get('/holiday-packages/search',[PackageController::class, 'all'])->name('packages.search');
Route::get('/packages/{package:slug}',[PackageController::class, 'show'])->name('packages.show');

// Enquire Now — guest-friendly lead capture, available on every package regardless of booking_type
Route::post('/packages/{package:slug}/enquire',            [PackageEnquiryController::class, 'store'])->name('packages.enquire.store');
Route::get('/packages/{package:slug}/enquiry/thank-you',   [PackageEnquiryController::class, 'thankYou'])->name('packages.enquire.thankyou');

// Book Now — requires login (guests are sent to login/register and returned here after auth)
Route::middleware('auth')->group(function () {
    Route::get('/packages/{package:slug}/book',  [BookingController::class, 'form'])->name('bookings.form');
    Route::post('/packages/{package:slug}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/packages/{package:slug}/apply-coupon', [BookingController::class, 'applyCoupon'])->name('bookings.apply-coupon');
    Route::post('/bookings/{booking:booking_reference}/retry',   [BookingController::class, 'retry'])->name('bookings.retry');

    // Book Now for a specific room in a specific hotel — same "auth required, PayU on
    // submit" shape as a package booking, just keyed off hotel + room type instead.
    Route::get('/hotels/{hotel:slug}/rooms/{roomType}/book',  [BookingController::class, 'hotelForm'])->name('bookings.hotel.form');
    Route::post('/hotels/{hotel:slug}/rooms/{roomType}/book', [BookingController::class, 'hotelStore'])->name('bookings.hotel.store');
    Route::post('/hotels/{hotel:slug}/rooms/{roomType}/apply-coupon', [BookingController::class, 'applyHotelCoupon'])->name('bookings.hotel.apply-coupon');
});

// Success/failed pages: kept outside the 'auth' group because PayU's redirect back here is a
// cross-site POST-then-redirect, which browsers drop the session cookie for under SameSite=Lax —
// an "auth" gate would bounce an already-logged-in customer to the login page. The controller
// instead accepts either a logged-in owner or the signed link handleCallback() generates.
Route::get('/bookings/{booking:booking_reference}/success',  [BookingController::class, 'success'])->name('bookings.success');
Route::get('/bookings/{booking:booking_reference}/failed',   [BookingController::class, 'failed'])->name('bookings.failed');

// PayU server-to-server style callbacks — posted from the customer's own browser after
// checkout, CSRF-exempt (see bootstrap/app.php), authenticated instead by response hash.
// Also opted out of session handling: since this POST arrives without the browser's real
// session cookie (SameSite=Lax drops it on a cross-site POST), StartSession would otherwise
// start a brand-new guest session and stamp its cookie onto the response — silently logging
// the customer out for the rest of their visit, even after they land back on our own domain.
// VerifyCsrfToken is dropped too: its token *check* is already excepted in bootstrap/app.php,
// but the middleware still tries to refresh the CSRF cookie via $request->session() at the end
// of handle() — with StartSession gone, no session store is bound to the request, so that
// throws "Session store not set on request" instead of quietly no-op'ing.
Route::post('/payu/callback/success', [BookingController::class, 'paymentSuccess'])
    ->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, VerifyCsrfToken::class])
    ->name('payu.callback.success');
Route::post('/payu/callback/failure', [BookingController::class, 'paymentFailure'])
    ->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, VerifyCsrfToken::class])
    ->name('payu.callback.failure');
Route::get('/activities',             [ActivityController::class, 'index'])->name('activities.index');
Route::get('/activities/search',      [ActivityController::class, 'index'])->name('activities.search');
Route::get('/activities/{activity:slug}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/hotels',                 [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel:slug}',    [HotelController::class, 'show'])->name('hotels.show');

// ── Autocomplete (destinations master doubles as the city/destination source) ──
Route::get('/api/locations', [LocationController::class, 'search'])->name('api.locations');

}); // end site.blocked group

// NOTE: /{slug} catch-all is registered in bootstrap/app.php `then` callback
// after all admin and user routes, so it never catches /login /register /crm/*
