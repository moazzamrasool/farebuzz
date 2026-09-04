<?php

/*
|--------------------------------------------------------------------------
| Admin Routes  —  prefix: /crm
|--------------------------------------------------------------------------
| Guard  : admin  (App\Models\Admin\Admin, admins table)
| Middleware: admin.guest  →  redirect authenticated admin to crm.dashboard
|             admin.auth   →  redirect unauthenticated to crm.login
*/

use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\ContactPageController;
use App\Http\Controllers\Admin\HomepageSeoController;
use App\Http\Controllers\Admin\ListingPageSeoController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CrmDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\EditorUploadController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\ExclusionController;
use App\Http\Controllers\Admin\AiBlogController;
use App\Http\Controllers\Admin\AiPackageController;
use App\Http\Controllers\Admin\HolidayPackageController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\HotelReviewController;
use App\Http\Controllers\Admin\InclusionController;
use App\Http\Controllers\Admin\LeadActivityController;
use App\Http\Controllers\Admin\LeadAssignmentController;
use App\Http\Controllers\Admin\LeadEmailController;
use App\Http\Controllers\Admin\LeadFollowUpController;
use App\Http\Controllers\Admin\LeadWhatsAppController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\Admin\NavbarMenuController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PackageEnquiryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SearchTabSettingController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Controllers\Admin\TrackingScriptController;
use App\Http\Controllers\Admin\ActivityCategoryController;
use App\Http\Controllers\Admin\TravelCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WhatsAppSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('crm')->group(function () {

    // ── Public (admin not yet logged in) ────────────────────────────────
    Route::middleware('admin.guest')->group(function () {
        Route::get('login',  [AdminLoginController::class, 'index'])->name('crm.login');
        Route::post('login', [AdminLoginController::class, 'authenticate'])->name('crm.authenticate');
    });

    // ── Protected (admin must be logged in) ─────────────────────────────
    Route::middleware(['admin.auth', 'tenant.resolve'])->group(function () {
        Route::get('dashboard',           [AdminDashboardController::class, 'index'])->name('crm.dashboard');
        Route::get('query',               [AdminDashboardController::class, 'query'])->name('crm.query');
        Route::get('page',                [AdminDashboardController::class, 'page'])->name('crm.page');
        Route::any('page/create/{id?}',   [AdminDashboardController::class, 'pagecreate'])->name('crm.page.create');
        Route::get('page/edit/{id}',      [AdminDashboardController::class, 'pageEdit'])->name('crm.page.edit');
        Route::get('setting',             [AdminDashboardController::class, 'setting'])->name('crm.setting');

        // Backs every CKEditor rich-text field's image-upload tab (see EditorUploadController).
        Route::post('editor/upload-image', [EditorUploadController::class, 'image'])->name('crm.editor.upload-image');

        // Business enquiries — Super Admin only (leads submitted from the public /crm landing page)
        Route::middleware('admin.super')->group(function () {
            Route::get('enquiries',                     [AdminEnquiryController::class, 'index'])->name('crm.enquiries.index');
            Route::post('enquiries/{enquiry}/approve',  [AdminEnquiryController::class, 'approve'])->name('crm.enquiries.approve');
            Route::post('enquiries/{enquiry}/reject',   [AdminEnquiryController::class, 'reject'])->name('crm.enquiries.reject');
        });

        // ── Master Data — per company (Admin/User tiers only, never Super Admin) ─
        Route::middleware('admin.company')->group(function () {
            // Travel Categories
            Route::get('travel-categories',                              [TravelCategoryController::class, 'index'])->name('crm.travel-categories.index');
            Route::get('travel-categories/create',                       [TravelCategoryController::class, 'create'])->name('crm.travel-categories.create');
            Route::post('travel-categories',                             [TravelCategoryController::class, 'store'])->name('crm.travel-categories.store');
            Route::get('travel-categories/{travelCategory}/edit',        [TravelCategoryController::class, 'edit'])->name('crm.travel-categories.edit');
            Route::put('travel-categories/{travelCategory}',             [TravelCategoryController::class, 'update'])->name('crm.travel-categories.update');
            Route::delete('travel-categories/{travelCategory}',          [TravelCategoryController::class, 'destroy'])->name('crm.travel-categories.destroy');
            Route::post('travel-categories/{travelCategory}/toggle-status', [TravelCategoryController::class, 'toggleStatus'])->name('crm.travel-categories.toggle-status');

            // Activity Categories
            Route::get('activity-categories',                                     [ActivityCategoryController::class, 'index'])->name('crm.activity-categories.index');
            Route::get('activity-categories/create',                              [ActivityCategoryController::class, 'create'])->name('crm.activity-categories.create');
            Route::post('activity-categories',                                    [ActivityCategoryController::class, 'store'])->name('crm.activity-categories.store');
            Route::get('activity-categories/{activityCategory}/edit',             [ActivityCategoryController::class, 'edit'])->name('crm.activity-categories.edit');
            Route::put('activity-categories/{activityCategory}',                  [ActivityCategoryController::class, 'update'])->name('crm.activity-categories.update');
            Route::delete('activity-categories/{activityCategory}',               [ActivityCategoryController::class, 'destroy'])->name('crm.activity-categories.destroy');
            Route::post('activity-categories/{activityCategory}/toggle-status',   [ActivityCategoryController::class, 'toggleStatus'])->name('crm.activity-categories.toggle-status');

            // Destinations
            Route::get('destinations',                               [DestinationController::class, 'index'])->name('crm.destinations.index');
            Route::get('destinations/create',                        [DestinationController::class, 'create'])->name('crm.destinations.create');
            Route::post('destinations',                              [DestinationController::class, 'store'])->name('crm.destinations.store');
            Route::get('destinations/{destination}/edit',            [DestinationController::class, 'edit'])->name('crm.destinations.edit');
            Route::put('destinations/{destination}',                 [DestinationController::class, 'update'])->name('crm.destinations.update');
            Route::delete('destinations/{destination}',              [DestinationController::class, 'destroy'])->name('crm.destinations.destroy');
            Route::post('destinations/{destination}/toggle-status',  [DestinationController::class, 'toggleStatus'])->name('crm.destinations.toggle-status');
            Route::middleware('admin.permission:destinations.create')->group(function () {
                Route::get('destinations/bulk-upload/template',      [DestinationController::class, 'downloadTemplate'])->name('crm.destinations.bulk-upload.template');
                Route::post('destinations/bulk-upload',               [DestinationController::class, 'bulkUpload'])->name('crm.destinations.bulk-upload');
                Route::get('destinations/bulk-upload/errors/{token}', [DestinationController::class, 'downloadErrorReport'])->name('crm.destinations.bulk-upload.errors');
            });

            // Activities
            Route::get('activities',                             [ActivityController::class, 'index'])->name('crm.activities.index');
            Route::get('activities/create',                      [ActivityController::class, 'create'])->name('crm.activities.create');
            Route::post('activities',                            [ActivityController::class, 'store'])->name('crm.activities.store');
            Route::get('activities/{activity}/edit',             [ActivityController::class, 'edit'])->name('crm.activities.edit');
            Route::put('activities/{activity}',                  [ActivityController::class, 'update'])->name('crm.activities.update');
            Route::delete('activities/{activity}',               [ActivityController::class, 'destroy'])->name('crm.activities.destroy');
            Route::post('activities/{activity}/toggle-status',   [ActivityController::class, 'toggleStatus'])->name('crm.activities.toggle-status');
            Route::middleware('admin.permission:activities.create')->group(function () {
                Route::get('activities/bulk-upload/template',      [ActivityController::class, 'downloadTemplate'])->name('crm.activities.bulk-upload.template');
                Route::post('activities/bulk-upload',                [ActivityController::class, 'bulkUpload'])->name('crm.activities.bulk-upload');
                Route::get('activities/bulk-upload/errors/{token}', [ActivityController::class, 'downloadErrorReport'])->name('crm.activities.bulk-upload.errors');
            });

            // Holiday Packages — rebuilt multi-tab builder, permission-enforced
            Route::middleware('admin.permission:holiday-packages.view')->get('holiday-packages', [HolidayPackageController::class, 'index'])->name('crm.holiday-packages.index');
            Route::middleware('admin.permission:holiday-packages.create')->group(function () {
                Route::get('holiday-packages/create', [HolidayPackageController::class, 'create'])->name('crm.holiday-packages.create');
                Route::post('holiday-packages', [HolidayPackageController::class, 'store'])->name('crm.holiday-packages.store');
                Route::get('holiday-packages/bulk-upload/template', [HolidayPackageController::class, 'downloadTemplate'])->name('crm.holiday-packages.bulk-upload.template');
                Route::post('holiday-packages/bulk-upload', [HolidayPackageController::class, 'bulkUpload'])->name('crm.holiday-packages.bulk-upload');
                Route::get('holiday-packages/bulk-upload/errors/{token}', [HolidayPackageController::class, 'downloadErrorReport'])->name('crm.holiday-packages.bulk-upload.errors');
                Route::post('holiday-packages/ai-generate', [AiPackageController::class, 'generate'])->name('crm.holiday-packages.ai-generate');
                Route::post('holiday-packages/ai-settings/toggle', [AiPackageController::class, 'toggleSetting'])->name('crm.holiday-packages.ai-settings.toggle');
            });
            Route::middleware('admin.permission:holiday-packages.edit')->group(function () {
                Route::get('holiday-packages/{holidayPackage}/edit', [HolidayPackageController::class, 'edit'])->name('crm.holiday-packages.edit');
                Route::put('holiday-packages/{holidayPackage}', [HolidayPackageController::class, 'update'])->name('crm.holiday-packages.update');
                Route::post('holiday-packages/{holidayPackage}/toggle-status', [HolidayPackageController::class, 'toggleStatus'])->name('crm.holiday-packages.toggle-status');
            });
            Route::middleware('admin.permission:holiday-packages.delete')->delete('holiday-packages/{holidayPackage}', [HolidayPackageController::class, 'destroy'])->name('crm.holiday-packages.destroy');

            // Bookings — read-only listing; invoice PDF route added alongside Part D
            Route::middleware('admin.permission:bookings.view')->group(function () {
                Route::get('bookings', [AdminBookingController::class, 'index'])->name('crm.bookings.index');
                Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('crm.bookings.show');
                Route::get('bookings/{booking}/invoice', [AdminBookingController::class, 'invoice'])->name('crm.bookings.invoice');
                Route::get('bookings/{booking}/itinerary', [AdminBookingController::class, 'itinerary'])->name('crm.bookings.itinerary');
                Route::get('bookings/{booking}/itinerary/preview', [AdminBookingController::class, 'previewItinerary'])->name('crm.bookings.itinerary.preview');
            });
            Route::middleware('admin.permission:bookings.edit')->post('bookings/{booking}/send-itinerary', [AdminBookingController::class, 'sendItinerary'])->name('crm.bookings.send-itinerary');
            // "Payment succeeded but our callback never arrived" recovery path — asks PayU
            // directly for this booking's latest txn status and confirms it the same way a
            // real callback would, if PayU confirms success and the amount matches.
            Route::middleware('admin.permission:bookings.edit')->post('bookings/{booking}/reconcile-payment', [AdminBookingController::class, 'reconcilePayment'])->name('crm.bookings.reconcile-payment');

            // Packages — module unused by the live site (no Holiday Package links to it);
            // hidden from the sidebar and routes disabled rather than deleted, in case it's revived later.
            // Route::get('packages',                             [PackageController::class, 'index'])->name('crm.packages.index');
            // Route::get('packages/create',                      [PackageController::class, 'create'])->name('crm.packages.create');
            // Route::post('packages',                            [PackageController::class, 'store'])->name('crm.packages.store');
            // Route::get('packages/{package}/edit',              [PackageController::class, 'edit'])->name('crm.packages.edit');
            // Route::put('packages/{package}',                   [PackageController::class, 'update'])->name('crm.packages.update');
            // Route::delete('packages/{package}',                [PackageController::class, 'destroy'])->name('crm.packages.destroy');
            // Route::post('packages/{package}/toggle-status',    [PackageController::class, 'toggleStatus'])->name('crm.packages.toggle-status');

            // Amenities — new module, permission-enforced
            Route::middleware('admin.permission:amenities.view')->get('amenities', [AmenityController::class, 'index'])->name('crm.amenities.index');
            Route::middleware('admin.permission:amenities.create')->post('amenities', [AmenityController::class, 'store'])->name('crm.amenities.store');
            Route::middleware('admin.permission:amenities.edit')->group(function () {
                Route::get('amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('crm.amenities.edit');
                Route::put('amenities/{amenity}', [AmenityController::class, 'update'])->name('crm.amenities.update');
                Route::post('amenities/{amenity}/toggle-status', [AmenityController::class, 'toggleStatus'])->name('crm.amenities.toggle-status');
            });
            Route::middleware('admin.permission:amenities.delete')->delete('amenities/{amenity}', [AmenityController::class, 'destroy'])->name('crm.amenities.destroy');

            // Hotels — new module, permission-enforced
            Route::middleware('admin.permission:hotels.view')->get('hotels', [HotelController::class, 'index'])->name('crm.hotels.index');
            Route::middleware('admin.permission:hotels.create')->group(function () {
                Route::get('hotels/create', [HotelController::class, 'create'])->name('crm.hotels.create');
                Route::post('hotels', [HotelController::class, 'store'])->name('crm.hotels.store');
                Route::get('hotels/bulk-upload/template', [HotelController::class, 'downloadTemplate'])->name('crm.hotels.bulk-upload.template');
                Route::post('hotels/bulk-upload', [HotelController::class, 'bulkUpload'])->name('crm.hotels.bulk-upload');
                Route::get('hotels/bulk-upload/errors/{token}', [HotelController::class, 'downloadErrorReport'])->name('crm.hotels.bulk-upload.errors');
            });
            Route::middleware('admin.permission:hotels.edit')->group(function () {
                Route::get('hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('crm.hotels.edit');
                Route::put('hotels/{hotel}', [HotelController::class, 'update'])->name('crm.hotels.update');
                Route::post('hotels/{hotel}/toggle-status', [HotelController::class, 'toggleStatus'])->name('crm.hotels.toggle-status');
            });
            Route::middleware('admin.permission:hotels.delete')->delete('hotels/{hotel}', [HotelController::class, 'destroy'])->name('crm.hotels.destroy');

            // Hotel Reviews — guest reviews shown on a hotel's public detail page
            Route::middleware('admin.permission:hotel-reviews.view')->get('hotel-reviews', [HotelReviewController::class, 'index'])->name('crm.hotel-reviews.index');
            Route::middleware('admin.permission:hotel-reviews.create')->post('hotel-reviews', [HotelReviewController::class, 'store'])->name('crm.hotel-reviews.store');
            Route::middleware('admin.permission:hotel-reviews.edit')->group(function () {
                Route::get('hotel-reviews/{hotelReview}/edit', [HotelReviewController::class, 'edit'])->name('crm.hotel-reviews.edit');
                Route::put('hotel-reviews/{hotelReview}', [HotelReviewController::class, 'update'])->name('crm.hotel-reviews.update');
            });
            Route::middleware('admin.permission:hotel-reviews.delete')->delete('hotel-reviews/{hotelReview}', [HotelReviewController::class, 'destroy'])->name('crm.hotel-reviews.destroy');

            // Inclusions & Exclusions — share the package_features table/UI, split by type
            Route::middleware('admin.permission:inclusions.view')->get('inclusions', [InclusionController::class, 'index'])->name('crm.inclusions.index');
            Route::middleware('admin.permission:inclusions.create')->post('inclusions', [InclusionController::class, 'store'])->name('crm.inclusions.store');
            Route::middleware('admin.permission:inclusions.edit')->group(function () {
                Route::get('inclusions/{feature}/edit', [InclusionController::class, 'edit'])->name('crm.inclusions.edit');
                Route::put('inclusions/{feature}', [InclusionController::class, 'update'])->name('crm.inclusions.update');
                Route::post('inclusions/{feature}/toggle-status', [InclusionController::class, 'toggleStatus'])->name('crm.inclusions.toggle-status');
            });
            Route::middleware('admin.permission:inclusions.delete')->delete('inclusions/{feature}', [InclusionController::class, 'destroy'])->name('crm.inclusions.destroy');

            Route::middleware('admin.permission:exclusions.view')->get('exclusions', [ExclusionController::class, 'index'])->name('crm.exclusions.index');
            Route::middleware('admin.permission:exclusions.create')->post('exclusions', [ExclusionController::class, 'store'])->name('crm.exclusions.store');
            Route::middleware('admin.permission:exclusions.edit')->group(function () {
                Route::get('exclusions/{feature}/edit', [ExclusionController::class, 'edit'])->name('crm.exclusions.edit');
                Route::put('exclusions/{feature}', [ExclusionController::class, 'update'])->name('crm.exclusions.update');
                Route::post('exclusions/{feature}/toggle-status', [ExclusionController::class, 'toggleStatus'])->name('crm.exclusions.toggle-status');
            });
            Route::middleware('admin.permission:exclusions.delete')->delete('exclusions/{feature}', [ExclusionController::class, 'destroy'])->name('crm.exclusions.destroy');

            // Coupons / Offers — "create" and "{coupon}/edit" registered before the bare
            // "{coupon}" show route so they aren't shadowed by it.
            Route::middleware('admin.permission:coupons.view')->get('coupons', [CouponController::class, 'index'])->name('crm.coupons.index');
            Route::middleware('admin.permission:coupons.create')->group(function () {
                Route::get('coupons/create', [CouponController::class, 'create'])->name('crm.coupons.create');
                Route::post('coupons', [CouponController::class, 'store'])->name('crm.coupons.store');
            });
            Route::middleware('admin.permission:coupons.edit')->group(function () {
                Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('crm.coupons.edit');
                Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('crm.coupons.update');
                Route::post('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('crm.coupons.toggle-status');
            });
            Route::middleware('admin.permission:coupons.delete')->delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('crm.coupons.destroy');
            Route::middleware('admin.permission:coupons.view')->get('coupons/{coupon}', [CouponController::class, 'show'])->name('crm.coupons.show');

            // ── Homepage / CMS ──────────────────────────────────────────
            Route::middleware('admin.permission:homepage-sections.view')->group(function () {
                Route::get('homepage-sections', [HomepageSectionController::class, 'index'])->name('crm.homepage-sections.index');
                Route::get('homepage-sections/{section:key}/edit', [HomepageSectionController::class, 'edit'])->name('crm.homepage-sections.edit');
            });
            Route::middleware('admin.permission:homepage-sections.edit')->group(function () {
                Route::put('homepage-sections/{section:key}', [HomepageSectionController::class, 'update'])->name('crm.homepage-sections.update');
                Route::post('homepage-sections/reorder', [HomepageSectionController::class, 'reorder'])->name('crm.homepage-sections.reorder');
                Route::post('homepage-sections/{section:key}/toggle-status', [HomepageSectionController::class, 'toggleStatus'])->name('crm.homepage-sections.toggle-status');
                Route::post('search-tab-settings', [SearchTabSettingController::class, 'update'])->name('crm.search-tab-settings.update');
            });

            Route::middleware('admin.permission:about-page.view')->get('about-page', [AboutPageController::class, 'edit'])->name('crm.about-page.edit');
            Route::middleware('admin.permission:about-page.edit')->put('about-page', [AboutPageController::class, 'update'])->name('crm.about-page.update');

            Route::middleware('admin.permission:homepage-seo.view')->get('homepage-seo', [HomepageSeoController::class, 'edit'])->name('crm.homepage-seo.edit');
            Route::middleware('admin.permission:homepage-seo.edit')->put('homepage-seo', [HomepageSeoController::class, 'update'])->name('crm.homepage-seo.update');

            // Listing pages SEO — india-packages/international-packages/hotels/activities,
            // one settings row per page_key (see ListingPageSeo::PAGES).
            Route::middleware('admin.permission:listing-page-seo.view')->group(function () {
                Route::get('listing-page-seo', [ListingPageSeoController::class, 'index'])->name('crm.listing-page-seo.index');
                Route::get('listing-page-seo/{page}', [ListingPageSeoController::class, 'edit'])->where('page', 'india-packages|international-packages|hotels|activities')->name('crm.listing-page-seo.edit');
            });
            Route::middleware('admin.permission:listing-page-seo.edit')->put('listing-page-seo/{page}', [ListingPageSeoController::class, 'update'])->where('page', 'india-packages|international-packages|hotels|activities')->name('crm.listing-page-seo.update');

            Route::middleware('admin.permission:contact-page.view')->get('contact-page', [ContactPageController::class, 'edit'])->name('crm.contact-page.edit');
            Route::middleware('admin.permission:contact-page.edit')->put('contact-page', [ContactPageController::class, 'update'])->name('crm.contact-page.update');

            // WhatsApp Bot settings — company-owner-only (tier check inside the controller,
            // same treatment as AiPackageController::toggleSetting).
            Route::get('whatsapp-settings', [WhatsAppSettingController::class, 'edit'])->name('crm.whatsapp-settings.edit');
            Route::put('whatsapp-settings', [WhatsAppSettingController::class, 'update'])->name('crm.whatsapp-settings.update');

            // SEO — sitemap.xml status/regeneration + robots.txt editor. Permission-gated
            // like any other module (reuses the already-seeded "settings" permission).
            Route::middleware('admin.permission:settings.view')->get('seo-settings', [SeoSettingController::class, 'edit'])->name('crm.seo-settings.edit');
            Route::middleware('admin.permission:settings.edit')->group(function () {
                Route::put('seo-settings/robots', [SeoSettingController::class, 'updateRobots'])->name('crm.seo-settings.robots.update');
                Route::put('seo-settings/sitemap-config', [SeoSettingController::class, 'updateSitemapConfig'])->name('crm.seo-settings.sitemap-config.update');
                Route::post('seo-settings/sitemap/regenerate', [SeoSettingController::class, 'regenerateSitemap'])->name('crm.seo-settings.sitemap.regenerate');
                Route::put('seo-settings/defaults', [SeoSettingController::class, 'updateDefaults'])->name('crm.seo-settings.defaults.update');
            });

            // Tracking & Scripts (header/body/footer raw code injection) — company-owner-only,
            // never tier=user, same treatment as WhatsApp Bot settings above.
            Route::get('tracking-scripts', [TrackingScriptController::class, 'edit'])->name('crm.tracking-scripts.edit');
            Route::put('tracking-scripts', [TrackingScriptController::class, 'update'])->name('crm.tracking-scripts.update');

            Route::middleware('admin.permission:cms-pages.view')->get('cms-pages', [CmsPageController::class, 'index'])->name('crm.cms-pages.index');
            Route::middleware('admin.permission:cms-pages.create')->group(function () {
                Route::get('cms-pages/create', [CmsPageController::class, 'create'])->name('crm.cms-pages.create');
                Route::post('cms-pages', [CmsPageController::class, 'store'])->name('crm.cms-pages.store');
                Route::post('cms-pages/{cmsPage}/duplicate', [CmsPageController::class, 'duplicate'])->name('crm.cms-pages.duplicate');
            });
            Route::middleware('admin.permission:cms-pages.edit')->group(function () {
                Route::get('cms-pages/{cmsPage}/edit', [CmsPageController::class, 'edit'])->name('crm.cms-pages.edit');
                Route::put('cms-pages/{cmsPage}', [CmsPageController::class, 'update'])->name('crm.cms-pages.update');
                Route::post('cms-pages/{cmsPage}/toggle-status', [CmsPageController::class, 'toggleStatus'])->name('crm.cms-pages.toggle-status');
            });
            Route::middleware('admin.permission:cms-pages.delete')->delete('cms-pages/{cmsPage}', [CmsPageController::class, 'destroy'])->name('crm.cms-pages.destroy');

            Route::middleware('admin.permission:blog.view')->get('blogs', [AdminBlogController::class, 'index'])->name('crm.blogs.index');
            Route::middleware('admin.permission:blog.create')->group(function () {
                Route::get('blogs/create', [AdminBlogController::class, 'create'])->name('crm.blogs.create');
                Route::post('blogs', [AdminBlogController::class, 'store'])->name('crm.blogs.store');
                Route::post('blogs/ai-generate', [AiBlogController::class, 'generate'])->name('crm.blogs.ai-generate');
            });
            Route::middleware('admin.permission:blog.edit')->group(function () {
                Route::get('blogs/{blog}/edit', [AdminBlogController::class, 'edit'])->name('crm.blogs.edit');
                Route::put('blogs/{blog}', [AdminBlogController::class, 'update'])->name('crm.blogs.update');
                Route::post('blogs/{blog}/toggle-status', [AdminBlogController::class, 'toggleStatus'])->name('crm.blogs.toggle-status');
            });
            Route::middleware('admin.permission:blog.delete')->delete('blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('crm.blogs.destroy');

            Route::middleware('admin.permission:navbar-menu.view')->get('navbar-menu', [NavbarMenuController::class, 'index'])->name('crm.navbar-menu.index');
            Route::middleware('admin.permission:navbar-menu.create')->group(function () {
                Route::post('navbar-menu', [NavbarMenuController::class, 'store'])->name('crm.navbar-menu.store');
                Route::post('navbar-menu/reorder', [NavbarMenuController::class, 'reorder'])->name('crm.navbar-menu.reorder');
            });
            Route::middleware('admin.permission:navbar-menu.edit')->group(function () {
                Route::get('navbar-menu/{navbarMenuItem}/edit', [NavbarMenuController::class, 'edit'])->name('crm.navbar-menu.edit');
                Route::put('navbar-menu/{navbarMenuItem}', [NavbarMenuController::class, 'update'])->name('crm.navbar-menu.update');
                Route::post('navbar-menu/{navbarMenuItem}/toggle-status', [NavbarMenuController::class, 'toggleStatus'])->name('crm.navbar-menu.toggle-status');
            });
            Route::middleware('admin.permission:navbar-menu.delete')->delete('navbar-menu/{navbarMenuItem}', [NavbarMenuController::class, 'destroy'])->name('crm.navbar-menu.destroy');
        });

        // ── Leads / Package Enquiries — deliberately OUTSIDE admin.company ─────
        // Every other tenant module blocks Super Admin (admin.company's job); Leads is
        // the one exception, since Super Admin is expected to see every tenant's leads.
        // TenantScope already bypasses tenant filtering for Super Admin, so these same
        // routes/views naturally show them everything without a separate screen.
        Route::middleware('admin.permission:leads.view')->group(function () {
            Route::get('package-enquiries', [PackageEnquiryController::class, 'index'])->name('crm.package-enquiries.index');
            Route::get('package-enquiries/{packageEnquiry}', [PackageEnquiryController::class, 'show'])->name('crm.package-enquiries.show');
            Route::get('package-enquiries/{packageEnquiry}/whatsapp', [LeadWhatsAppController::class, 'compose'])->name('crm.package-enquiries.whatsapp.compose');
            Route::get('package-enquiries/{packageEnquiry}/email', [LeadEmailController::class, 'compose'])->name('crm.package-enquiries.email.compose');
            Route::get('follow-ups/due', [LeadFollowUpController::class, 'dueToday'])->name('crm.follow-ups.due');
            Route::get('leads-dashboard', [CrmDashboardController::class, 'index'])->name('crm.leads.dashboard');
            Route::get('message-templates', [MessageTemplateController::class, 'index'])->name('crm.message-templates.index');

            // Quotations — dedicated page per lead, viewing/downloading a past quote
            Route::get('package-enquiries/{packageEnquiry}/quotations/create', [QuotationController::class, 'create'])->name('crm.quotations.create');
            Route::post('package-enquiries/{packageEnquiry}/quotations/preview', [QuotationController::class, 'previewDraft'])->name('crm.quotations.preview-draft');
            Route::get('quotations/{quotation}', [QuotationController::class, 'show'])->name('crm.quotations.show');
            Route::get('quotations/{quotation}/download', [QuotationController::class, 'download'])->name('crm.quotations.download');
            Route::get('quotations/{quotation}/preview', [QuotationController::class, 'preview'])->name('crm.quotations.preview');
            Route::get('package-enquiries/{packageEnquiry}/itinerary/preview', [PackageEnquiryController::class, 'previewItinerary'])->name('crm.package-enquiries.itinerary.preview');
        });

        Route::middleware('admin.permission:leads.edit')->group(function () {
            Route::put('package-enquiries/{packageEnquiry}/status', [PackageEnquiryController::class, 'updateStatus'])->name('crm.package-enquiries.update-status');
            Route::put('package-enquiries/{packageEnquiry}/assign', [LeadAssignmentController::class, 'update'])->name('crm.package-enquiries.assign');
            Route::post('package-enquiries/{packageEnquiry}/notes', [LeadActivityController::class, 'store'])->name('crm.package-enquiries.notes.store');
            Route::post('package-enquiries/{packageEnquiry}/follow-ups', [LeadFollowUpController::class, 'store'])->name('crm.package-enquiries.follow-ups.store');
            Route::post('package-enquiries/{packageEnquiry}/follow-ups/{followUp}/complete', [LeadFollowUpController::class, 'complete'])->name('crm.package-enquiries.follow-ups.complete');
            Route::post('package-enquiries/{packageEnquiry}/whatsapp', [LeadWhatsAppController::class, 'send'])->name('crm.package-enquiries.whatsapp.send');
            Route::post('package-enquiries/{packageEnquiry}/email', [LeadEmailController::class, 'send'])->name('crm.package-enquiries.email.send');
            Route::post('package-enquiries/{packageEnquiry}/itinerary', [PackageEnquiryController::class, 'sendItinerary'])->name('crm.package-enquiries.itinerary.send');
            Route::post('package-enquiries/{packageEnquiry}/quotations', [QuotationController::class, 'store'])->name('crm.quotations.store');

            Route::get('message-templates/create', [MessageTemplateController::class, 'create'])->name('crm.message-templates.create');
            Route::post('message-templates', [MessageTemplateController::class, 'store'])->name('crm.message-templates.store');
            Route::get('message-templates/{messageTemplate}/edit', [MessageTemplateController::class, 'edit'])->name('crm.message-templates.edit');
            Route::put('message-templates/{messageTemplate}', [MessageTemplateController::class, 'update'])->name('crm.message-templates.update');
        });

        Route::middleware('admin.permission:leads.delete')->group(function () {
            Route::delete('package-enquiries/{packageEnquiry}', [PackageEnquiryController::class, 'destroy'])->name('crm.package-enquiries.destroy');
            Route::delete('message-templates/{messageTemplate}', [MessageTemplateController::class, 'destroy'])->name('crm.message-templates.destroy');
        });

        // ── Companies / Admins — Super Admin only ───────────────────────
        Route::middleware('admin.super')->group(function () {
            Route::get('admins',                          [AdminController::class, 'index'])->name('crm.admins.index');
            Route::get('admins/create',                    [AdminController::class, 'create'])->name('crm.admins.create');
            Route::post('admins',                          [AdminController::class, 'store'])->name('crm.admins.store');
            Route::get('admins/{admin}/edit',               [AdminController::class, 'edit'])->name('crm.admins.edit');
            Route::put('admins/{admin}',                    [AdminController::class, 'update'])->name('crm.admins.update');
            Route::delete('admins/{admin}',                 [AdminController::class, 'destroy'])->name('crm.admins.destroy');
            Route::post('admins/{admin}/block',             [AdminController::class, 'block'])->name('crm.admins.block');
            Route::post('admins/{admin}/unblock',           [AdminController::class, 'unblock'])->name('crm.admins.unblock');
        });

        // ── Roles & Permissions — per-company (Admin has implicit access, sub-admins need the permission) ─
        Route::middleware('admin.permission:roles.view')->get('roles',                [RoleController::class, 'index'])->name('crm.roles.index');
        Route::middleware('admin.permission:roles.create')->group(function () {
            Route::get('roles/create', [RoleController::class, 'create'])->name('crm.roles.create');
            Route::post('roles',       [RoleController::class, 'store'])->name('crm.roles.store');
        });
        Route::middleware('admin.permission:roles.edit')->group(function () {
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('crm.roles.edit');
            Route::put('roles/{role}',      [RoleController::class, 'update'])->name('crm.roles.update');
        });
        Route::middleware('admin.permission:roles.delete')->delete('roles/{role}', [RoleController::class, 'destroy'])->name('crm.roles.destroy');

        // ── Users / Sub-admins — per-company ─────────────────────────────
        Route::middleware('admin.permission:users.view')->get('users',                [UserController::class, 'index'])->name('crm.users.index');
        Route::middleware('admin.permission:users.create')->group(function () {
            Route::get('users/create', [UserController::class, 'create'])->name('crm.users.create');
            Route::post('users',       [UserController::class, 'store'])->name('crm.users.store');
        });
        Route::middleware('admin.permission:users.edit')->group(function () {
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('crm.users.edit');
            Route::put('users/{user}',      [UserController::class, 'update'])->name('crm.users.update');
        });
        Route::middleware('admin.permission:users.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('crm.users.destroy');

        // ── Customers — the public-facing signup pool, filtered to this company's bookings/enquiries ─
        Route::middleware('admin.permission:customers.view')->group(function () {
            Route::get('customers',            [CustomerController::class, 'index'])->name('crm.customers.index');
            Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('crm.customers.show');
        });
        Route::middleware('admin.permission:customers.edit')->group(function () {
            Route::post('customers/{customer}/verify',              [CustomerController::class, 'verify'])->name('crm.customers.verify');
            Route::post('customers/{customer}/toggle-status',       [CustomerController::class, 'toggleStatus'])->name('crm.customers.toggle-status');
            Route::post('customers/{customer}/resend-verification', [CustomerController::class, 'resendVerification'])->name('crm.customers.resend-verification');
        });

        Route::get('logout',              [AdminLoginController::class,     'logout'])->name('crm.logout');
    });
});
