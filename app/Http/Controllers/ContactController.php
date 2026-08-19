<?php

namespace App\Http\Controllers;

use App\Models\ContactPage;

class ContactController extends Controller
{
    public function show()
    {
        $contactPage = ContactPage::forSite()->first() ?? new ContactPage();

        return view('contact_us', compact('contactPage'));
    }
}
