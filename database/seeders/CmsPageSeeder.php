<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;

// Every footer/nav link without a dedicated feature resolves to one of these
// CMS pages by slug (see the "footer" section seeded in HomepageContentSeeder).
class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'about-us', 'title' => 'About Us', 'body' => '<p>FareBuzz is your trusted travel companion for flights, hotels, holidays, trains and more. We partner with the best hotels, airlines and local experts to bring you curated travel experiences at the best prices.</p>'],
            ['slug' => 'careers', 'title' => 'Careers', 'body' => '<p>We are always looking for passionate people to join the FareBuzz team. Check back soon for open roles, or write to us at careers@farebuzz.com.</p>'],
            ['slug' => 'news-blog', 'title' => 'News & Blog', 'body' => '<p>Travel stories, destination guides and company updates from the FareBuzz team.</p>'],
            ['slug' => 'investor-relations', 'title' => 'Investor Relations', 'body' => '<p>For investor queries, please reach out to our corporate office.</p>'],
            ['slug' => 'partner-with-us', 'title' => 'Partner With Us', 'body' => '<p>Run a hotel, resort or travel service? Partner with FareBuzz to reach thousands of travellers. Write to us at partners@farebuzz.com.</p>'],
            ['slug' => 'help-center', 'title' => 'Help Center', 'body' => '<p>Need help with a booking? Browse our frequently asked questions or contact our support team.</p>'],
            ['slug' => 'cancellation-policy', 'title' => 'Cancellation Policy', 'body' => '<p>Cancellation terms vary by package and hotel. Please refer to your booking confirmation for specific cancellation terms, or contact support for assistance.</p>'],
            ['slug' => 'travel-insurance', 'title' => 'Travel Insurance', 'body' => '<p>Protect your trip with comprehensive travel insurance covering medical emergencies, trip cancellations and lost baggage.</p>'],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'body' => '<p>We respect your privacy. This policy explains what information we collect and how it is used to provide and improve our services.</p>'],
            ['slug' => 'terms-of-service', 'title' => 'Terms of Service', 'body' => '<p>By using FareBuzz, you agree to these terms of service governing bookings, payments, cancellations and use of our website.</p>'],
            ['slug' => 'cookie-policy', 'title' => 'Cookie Policy', 'body' => '<p>FareBuzz uses cookies to improve your browsing experience, remember your preferences and analyse site traffic.</p>'],
            ['slug' => 'sitemap', 'title' => 'Sitemap', 'body' => '<p>An overview of all pages on FareBuzz — Home, India Packages, International Packages, Activities, MICE, and more.</p>'],
        ];

        foreach ($pages as $page) {
            CmsPage::firstOrCreate(
                ['slug' => $page['slug']],
                [
                    'unique_id' => SiteTenant::id(),
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'meta_title' => $page['title'].' – FareBuzz',
                    'status' => 'active',
                ]
            );
        }
    }
}
