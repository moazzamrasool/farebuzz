<?php

namespace App\Http\Controllers;

use App\Services\Seo\SitemapGenerator;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(SitemapGenerator $sitemap): Response
    {
        return response($sitemap->render(), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
