<?php

namespace App\Http\Controllers;

use App\Models\SeoSetting;
use App\Services\Seo\RobotsTxtBuilder;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $robotsTxt = SeoSetting::forSite()->first()?->robots_txt;

        return response(RobotsTxtBuilder::render($robotsTxt), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
