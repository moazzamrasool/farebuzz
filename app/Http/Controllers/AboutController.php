<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\AboutPageItem;

class AboutController extends Controller
{
    public function show()
    {
        $about = AboutPage::forSite()->first() ?? new AboutPage();

        $items = [];
        foreach (array_keys(AboutPageItem::SECTIONS) as $key) {
            $items[$key] = AboutPageItem::forSite()->section($key)->active()->ordered()->get();
        }

        return view('about.show', compact('about', 'items'));
    }
}
