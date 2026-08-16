<?php

namespace App\Http\Controllers;

use App\Mail\UserQueryMail;
use App\Models\CmsPage;
use App\Models\HomepageSection;
use App\Models\SearchTabSetting;
use App\Models\UserQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    // Homepage — public. Every section is CRM-managed; see resources/views/homepage/.
    public function welcome()
    {
        $states = DB::table('states')->get();
        $sections = HomepageSection::forSite()->active()->orderBy('sort_order')->get();
        $searchTabs = SearchTabSetting::forSite()->ordered()->get();

        return view('welcome', compact('states', 'sections', 'searchTabs'));
    }

    // AJAX — cities by state
    public function getCities($state)
    {
        $cities = DB::table('cities')->where('state_id', $state)->get();
        return response()->json(['cities' => $cities]);
    }

    // Submit inquiry form
    public function submitQuery(Request $request)
    {
        $request->validate([
            'firstname'   => 'required|string|max:255',
            'lastname'    => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|max:20',
            'state'       => 'required|numeric',
            'destination' => 'required|numeric',
            'message'     => 'string',
            'form_type'   => 'string|max:255',
        ]);

        $query              = new UserQuery();
        $query->firstname   = $request->firstname;
        $query->lastname    = $request->lastname;
        $query->email       = $request->email;
        $query->phone       = $request->phone;
        $query->state       = $request->state;
        $query->destination = $request->destination;
        $query->message     = $request->message;
        $query->form_type   = $request->form_type;
        $query->save();

        Mail::to(config('mail.admin_address'))->send(new UserQueryMail($query));

        if (Mail::failures()) {
            return response()->json(['error' => 'Failed to send your message. Please try again.'], 500);
        }

        return response()->json(['success' => 'Your message has been sent successfully!']);
    }

    // Submit contact form
    public function submitContactForm(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        $query            = new UserQuery();
        $query->firstname = $request->name;
        $query->email     = $request->email;
        $query->phone     = $request->phone;
        $query->message   = $request->message;
        $query->form_type = 'contact_form';
        $query->save();

        $state      = DB::table('states')->where('id', $query->state)->first();
        $city       = DB::table('cities')->where('id', $query->destination)->first();
        $query->state       = $state ? $state->state : '';
        $query->destination = $city  ? $city->name   : '';

        Mail::to(config('mail.admin_address'))->send(new UserQueryMail($query));

        if (Mail::failures()) {
            return response()->json(['error' => 'Failed to send your message. Please try again.'], 500);
        }

        return response()->json(['success' => 'Your message has been sent successfully!']);
    }

    // Dynamic CMS page by slug — must be last route in web.php.
    // Every footer/nav link without a dedicated feature points here.
    public function pages($slug)
    {
        $page = CmsPage::forSite()->active()->where('slug', $slug)->first();

        abort_unless($page, 404);

        return view('cms.show', compact('page'));
    }
}
