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
            // 'about-us' is intentionally NOT a CMS page — that slug is served by
            // AboutController (the rich Hero/Story/Team/Stats page edited at
            // /crm/about-page). A CMS page here would compete for the same URL.
            ['slug' => 'careers', 'title' => 'Careers', 'body' => $this->careersBody()],
            ['slug' => 'news-blog', 'title' => 'News & Blog', 'body' => '<p>Travel stories, destination guides and company updates from the FareBuzzer team.</p>'],
            ['slug' => 'investor-relations', 'title' => 'Investor Relations', 'body' => $this->investorRelationsBody()],
            ['slug' => 'partner-with-us', 'title' => 'Partner With Us', 'body' => $this->partnerWithUsBody()],
            ['slug' => 'help-center', 'title' => 'Help Center', 'body' => $this->helpCenterBody()],
            ['slug' => 'cancellation-policy', 'title' => 'Cancellation Policy', 'body' => $this->cancellationPolicyBody()],
            ['slug' => 'travel-insurance', 'title' => 'Travel Insurance', 'body' => $this->travelInsuranceBody()],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'body' => $this->privacyPolicyBody()],
            ['slug' => 'terms-of-service', 'title' => 'Terms of Service', 'body' => $this->termsOfServiceBody()],
            ['slug' => 'cookie-policy', 'title' => 'Cookie Policy', 'body' => $this->cookiePolicyBody()],
            ['slug' => 'sitemap', 'title' => 'Sitemap', 'body' => $this->sitemapBody()],
        ];

        foreach ($pages as $page) {
            $attributes = [
                'unique_id' => SiteTenant::id(),
                'title' => $page['title'],
                'body' => $page['body'],
                'meta_title' => $page['title'].' – FareBuzzer',
                'status' => 'active',
            ];

            // These slugs carry the designed "cms-*" reference templates (see the
            // ...Body() methods below) — kept in sync with the seeder on every run so
            // they stay reliable pages to duplicate/copy from. Every other slug only
            // fills in a body on first create, so re-seeding never clobbers real edits
            // an admin has since made.
            $designedSlugs = [
                'careers', 'help-center', 'travel-insurance', 'partner-with-us', 'investor-relations',
                'cancellation-policy', 'privacy-policy', 'terms-of-service', 'cookie-policy', 'sitemap',
            ];
            if (in_array($page['slug'], $designedSlugs, true)) {
                CmsPage::updateOrCreate(['slug' => $page['slug']], $attributes);
            } else {
                CmsPage::firstOrCreate(['slug' => $page['slug']], $attributes);
            }
        }
    }

    // Reference design for the CMS "Insert template" style pages — see the cms-* classes
    // in public/frontend/asset/css/style.css. Duplicate this CmsPage row and swap the
    // text/images to reuse it for Help Center, Partner With Us, etc.
    private function careersBody(): string
    {
        $hero = asset('frontend/img/cms/careers-hero.svg');
        $life = asset('frontend/img/cms/careers-life.svg');

        return <<<HTML
<section class="cms-hero" style="background-image:url('{$hero}');">
  <div class="cms-hero-inner">
    <h1>Build Your Career at FareBuzzer</h1>
    <p>Join a team that's obsessed with making travel effortless, personal and a little bit magical — for millions of travellers and for each other.</p>
  </div>
</section>

<p>We are always looking for passionate, curious people to join the FareBuzzer team. Whether you're into product, technology, travel operations or customer care, we're building a culture where good ideas travel fast and everyone gets to do the best work of their career. Check back soon for open roles, or write to us any time at <a href="mailto:careers@farebuzzertravel.com">careers@farebuzzertravel.com</a> — we'd love to hear from you.</p>

<h2>Why Work With Us</h2>
<div class="cms-cards">
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-airplane"></i></div>
    <h5>Travel Perks</h5>
    <p>Exclusive staff fares and holiday discounts on FareBuzzer packages, so wanderlust never has to wait for a vacation.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
    <h5>Growth &amp; Learning</h5>
    <p>Structured mentorship, skill workshops and a clear path to grow from where you start to where you want to be.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-people"></i></div>
    <h5>Culture &amp; Belonging</h5>
    <p>A warm, no-ego team that celebrates wins together and has each other's back on the tough days.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-laptop"></i></div>
    <h5>Flexible Work</h5>
    <p>Hybrid schedules and flexible hours built around how you do your best work, not a fixed clock.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-heart-pulse"></i></div>
    <h5>Health &amp; Wellness</h5>
    <p>Comprehensive health cover and wellness support for you and the people you care about.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-map"></i></div>
    <h5>Team Trips</h5>
    <p>Regular offsites and team trips to the destinations we sell — because we believe in what we ship.</p>
  </div>
</div>

<div class="cms-row">
  <div class="cms-row-media"><img src="{$life}" alt="FareBuzzer team collaborating around a desk" loading="lazy"></div>
  <div class="cms-row-text">
    <h2>Life at FareBuzzer</h2>
    <p>Our office runs on open desks, weekly demo sessions and a lot of shared travel stories. We're a team that plans other people's dream trips all day — and takes plenty of our own too, with regular offsites to the destinations on our own packages. Casual Fridays, spontaneous coffee catch-ups and a genuine "ask anyone anything" culture keep things human, even as we grow fast.</p>
  </div>
</div>

<h2>Open Roles</h2>
<div class="cms-table-wrap">
  <div class="cms-table-empty">
    <i class="bi bi-briefcase" style="font-size:28px;color:#005fcc;display:block;margin-bottom:10px;"></i>
    We don't have any open positions right now — but we're always looking for passionate people to join the FareBuzzer team. Check back soon, or write to us at <a href="mailto:careers@farebuzzertravel.com">careers@farebuzzertravel.com</a> and we'll reach out when a role that fits opens up.
  </div>
</div>

<div class="cms-cta">
  <h2>How to Apply</h2>
  <p>Don't see an open role that matches your skills? Send us your resume and a short note about what you'd like to work on — we keep every application on file and reach out as soon as a fit opens up.</p>
  <a class="cms-btn" href="mailto:careers@farebuzzertravel.com">Email careers@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Standard Content Page + FAQ" template — category cards to route travellers to the
    // right topic, then an accordion for the questions that don't need a full page.
    private function helpCenterBody(): string
    {
        $hero = asset('frontend/img/cms/help-center-hero.svg');

        return <<<HTML
<section class="cms-hero" style="background-image:url('{$hero}');">
  <div class="cms-hero-inner">
    <h1>How Can We Help You?</h1>
    <p>Answers to the most common booking, payment and travel questions — or reach a real person in a couple of taps.</p>
  </div>
</section>

<p>Whether you're planning a trip, waiting on a refund, or need a document reissued, our Help Center is the fastest place to start. Browse a topic below or search the frequently asked questions — and if you can't find what you need, our support team is just an email or call away.</p>

<h2>Browse by Topic</h2>
<div class="cms-cards">
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-credit-card"></i></div>
    <h5>Bookings &amp; Payments</h5>
    <p>Making a booking, payment methods, EMI options and invoice/GST copies.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
    <h5>Cancellations &amp; Refunds</h5>
    <p>How to cancel, refund timelines and tracking the status of your money back.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-pencil-square"></i></div>
    <h5>Trip Changes</h5>
    <p>Rescheduling dates, changing travellers, or upgrading your package.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-person-circle"></i></div>
    <h5>Account &amp; Profile</h5>
    <p>Login issues, updating contact details and managing saved travellers.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-file-earmark-text"></i></div>
    <h5>Travel Documents</h5>
    <p>E-tickets, hotel vouchers, visa help and identity document requirements.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-headset"></i></div>
    <h5>Contact Support</h5>
    <p>Reach our team directly for anything urgent or not covered here.</p>
  </div>
</div>

<h2>Frequently Asked Questions</h2>
<div class="cms-accordion">
  <details open>
    <summary>How do I cancel or modify my booking?</summary>
    <p>Go to My Trips in your account and select the booking you'd like to change. Cancellation and rescheduling options — along with any applicable charges — are shown before you confirm.</p>
  </details>
  <details>
    <summary>When will I receive my refund?</summary>
    <p>Approved refunds are usually processed within 7–10 business days to your original payment method, though your bank may take a few extra days to reflect it.</p>
  </details>
  <details>
    <summary>How do I download my e-ticket or hotel voucher?</summary>
    <p>Your e-ticket and vouchers are emailed on confirmation and are also available any time under My Trips &gt; Booking Details &gt; Download.</p>
  </details>
  <details>
    <summary>Do I need to reconfirm my flight before departure?</summary>
    <p>We recommend reconfirming international flights 24–48 hours before departure directly with the airline, as schedules can occasionally change.</p>
  </details>
  <details>
    <summary>How do I contact customer support?</summary>
    <p>Email us at <a href="mailto:enquiry@farebuzzertravel.com">enquiry@farebuzzertravel.com</a> or use the chat widget in the bottom corner of any page — our team typically responds within a few hours.</p>
  </details>
</div>

<div class="cms-cta">
  <h2>Still Need Help?</h2>
  <p>Our support team is available every day to help with bookings, changes and anything else on your trip.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Landing Page with CTA" template — coverage cards, a compliance-style callout, FAQ,
    // then a CTA. Product/benefits pages like this one lean on cards + callout, not a
    // people photo, since there's no "life at the company" moment to show.
    private function travelInsuranceBody(): string
    {
        $hero = asset('frontend/img/cms/travel-insurance-hero.svg');

        return <<<HTML
<section class="cms-hero" style="background-image:url('{$hero}');">
  <div class="cms-hero-inner">
    <h1>Travel With Confidence</h1>
    <p>Comprehensive coverage for medical emergencies, trip cancellations and lost baggage — because the best trips plan for the unexpected too.</p>
  </div>
</section>

<p>Protect your trip with travel insurance covering medical emergencies, cancellations and lost baggage. Add a plan at checkout or any time before departure, and travel knowing you're covered if plans change.</p>

<h2>What's Covered</h2>
<div class="cms-cards">
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-heart-pulse"></i></div>
    <h5>Medical Emergencies</h5>
    <p>Hospitalisation, emergency treatment and ambulance costs while you're travelling.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-x-circle"></i></div>
    <h5>Trip Cancellation</h5>
    <p>Recover prepaid, non-refundable costs if you have to cancel for a covered reason.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-suitcase"></i></div>
    <h5>Lost Baggage</h5>
    <p>Compensation for baggage that's lost, delayed or damaged in transit.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-clock-history"></i></div>
    <h5>Flight Delay</h5>
    <p>Cover for reasonable extra expenses caused by a significantly delayed flight.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-airplane-engines"></i></div>
    <h5>Emergency Evacuation</h5>
    <p>Emergency medical evacuation and repatriation when you need it most.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-telephone-inbound"></i></div>
    <h5>24/7 Assistance</h5>
    <p>A round-the-clock helpline for emergencies anywhere in the world.</p>
  </div>
</div>

<div class="cms-callout">
  <p><strong>Good to know:</strong> Coverage, limits and exclusions vary by plan and destination. Please read the policy wording carefully before you buy, and check pre-existing condition clauses if relevant to you.</p>
</div>

<h2>Frequently Asked Questions</h2>
<div class="cms-accordion">
  <details open>
    <summary>What does travel insurance cover?</summary>
    <p>Most plans cover medical emergencies, trip cancellation/interruption, baggage loss or delay, and flight delays — exact coverage depends on the plan you choose.</p>
  </details>
  <details>
    <summary>Can I buy insurance after booking my trip?</summary>
    <p>Yes — you can add a plan any time before departure, though some benefits (like cancellation cover) work best when purchased soon after booking.</p>
  </details>
  <details>
    <summary>How do I file a claim?</summary>
    <p>Contact the insurer's claims helpline (in your policy document) as soon as possible and keep all receipts and reports — our support team can help you get started.</p>
  </details>
  <details>
    <summary>Is coverage available for pre-existing medical conditions?</summary>
    <p>Some plans offer limited cover for pre-existing conditions. Check the specific plan's terms or contact us before you buy if this applies to you.</p>
  </details>
</div>

<div class="cms-cta">
  <h2>Add Travel Insurance to Your Trip</h2>
  <p>Talk to our team about the right plan for your destination and travel dates.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Landing Page with CTA" template, B2B flavour — benefit cards, a two-column "how it
    // works" row and a stats bar to build trust before the ask.
    private function partnerWithUsBody(): string
    {
        $hero = asset('frontend/img/cms/partner-hero.svg');
        $handshake = asset('frontend/img/cms/partner-handshake.svg');

        return <<<HTML
<section class="cms-hero" style="background-image:url('{$hero}');">
  <div class="cms-hero-inner">
    <h1>Partner With FareBuzzer</h1>
    <p>Run a hotel, resort or travel service? Join our growing network and reach thousands of travellers every month.</p>
  </div>
</section>

<p>We work with hotels, resorts, activity operators and travel service providers across India and beyond to build packages travellers love. Partnering with FareBuzzer means more visibility, easier bookings and a team that's genuinely invested in your growth. Write to us at <a href="mailto:enquiry@farebuzzertravel.com">enquiry@farebuzzertravel.com</a> to get started.</p>

<h2>Why Partner With Us</h2>
<div class="cms-cards">
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-broadcast"></i></div>
    <h5>Wider Reach</h5>
    <p>Get discovered by travellers actively planning trips to your destination.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-rocket-takeoff"></i></div>
    <h5>Easy Onboarding</h5>
    <p>A simple, guided setup — most partners go live within days, not weeks.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-cash-coin"></i></div>
    <h5>Timely Payouts</h5>
    <p>Transparent commission structure and payouts on a predictable schedule.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-person-workspace"></i></div>
    <h5>Dedicated Support</h5>
    <p>A partner success contact who actually knows your account.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-megaphone"></i></div>
    <h5>Marketing Boost</h5>
    <p>Featured placement in our campaigns, packages and seasonal promotions.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-sliders"></i></div>
    <h5>Flexible Terms</h5>
    <p>Rates, availability and inventory stay in your control at all times.</p>
  </div>
</div>

<div class="cms-row">
  <div class="cms-row-media"><img src="{$handshake}" alt="FareBuzzer team closing a partnership deal" loading="lazy"></div>
  <div class="cms-row-text">
    <h2>How Partnership Works</h2>
    <p>Getting started is simple: apply with a few details about your property or service, our partnerships team reviews and onboards you onto our system, and once your listing is live, bookings start flowing in — with our support team on hand throughout.</p>
  </div>
</div>

<div class="cms-stats">
  <div class="cms-stat"><div class="cms-stat-value">500+</div><div class="cms-stat-label">Partner Properties</div></div>
  <div class="cms-stat"><div class="cms-stat-value">50+</div><div class="cms-stat-label">Destinations</div></div>
  <div class="cms-stat"><div class="cms-stat-value">2M+</div><div class="cms-stat-label">Travellers Reached</div></div>
  <div class="cms-stat"><div class="cms-stat-value">4.7★</div><div class="cms-stat-label">Avg. Partner Rating</div></div>
</div>

<div class="cms-cta">
  <h2>Ready to Grow With Us?</h2>
  <p>Tell us about your property or service and our partnerships team will reach out within a couple of business days.</p>
  <a class="cms-btn cms-btn-orange" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // Corporate/IR template — a stats bar for credibility, a compliance-style callout, and
    // a direct contact route rather than product-style benefit cards.
    private function investorRelationsBody(): string
    {
        $hero = asset('frontend/img/cms/investor-hero.svg');

        return <<<HTML
<section class="cms-hero" style="background-image:url('{$hero}');">
  <div class="cms-hero-inner">
    <h1>Investor Relations</h1>
    <p>Building the future of travel planning — transparently, responsibly, and with an eye on long-term value.</p>
  </div>
</section>

<p>FareBuzzer is building a technology-led travel platform for the next generation of travellers. This page is a starting point for investor queries — for detailed financials, governance information or partnership discussions, please reach out to our Investor Relations desk directly.</p>

<h2>FareBuzzer at a Glance</h2>
<div class="cms-stats">
  <div class="cms-stat"><div class="cms-stat-value">2019</div><div class="cms-stat-label">Founded</div></div>
  <div class="cms-stat"><div class="cms-stat-value">12+</div><div class="cms-stat-label">Cities</div></div>
  <div class="cms-stat"><div class="cms-stat-value">2M+</div><div class="cms-stat-label">Travellers Served</div></div>
  <div class="cms-stat"><div class="cms-stat-value">150+</div><div class="cms-stat-label">Team Members</div></div>
</div>

<div class="cms-callout">
  <p><strong>Note:</strong> The figures above are a general company snapshot and not audited financial statements. For detailed reports, disclosures or governance documentation, please contact our Investor Relations desk.</p>
</div>

<h2>Get in Touch</h2>
<div class="cms-row">
  <div class="cms-row-text" style="flex:1 1 100%;">
    <p>For investor queries, partnership discussions or corporate documentation requests, please reach out to our corporate office. Our Investor Relations desk aims to respond to all queries within 3–5 business days.</p>
  </div>
</div>

<div class="cms-cta">
  <h2>Contact Investor Relations</h2>
  <p>Reach our corporate team directly for financial queries, partnership discussions or documentation requests.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Policy/Legal Page" template — photo-free gradient hero, a compliance callout, a
    // terms table instead of cards, and an FAQ accordion. Deliberately text-first: legal
    // pages read as reference material, not a landing page.
    private function cancellationPolicyBody(): string
    {
        return <<<HTML
<section class="cms-hero cms-hero-plain">
  <div class="cms-hero-inner">
    <h1>Cancellation Policy</h1>
    <p>Simple, transparent terms so you always know where you stand.</p>
  </div>
</section>

<p>Cancellation terms vary by package, hotel and how close to departure you cancel. Your booking confirmation always has the final word on the specific terms that apply to your trip — the table below is a general guide, and our support team is happy to help if anything is unclear.</p>

<div class="cms-callout">
  <p><strong>Please note:</strong> Some hotels, airlines and third-party suppliers apply their own, sometimes stricter, cancellation terms. Where this applies, the supplier's policy takes precedence over the general terms below.</p>
</div>

<h2>General Cancellation Charges</h2>
<div class="cms-table-wrap">
  <table>
    <thead>
      <tr><th>Time Before Departure</th><th>Cancellation Charge</th></tr>
    </thead>
    <tbody>
      <tr><td>30 days or more</td><td>10% of package cost</td></tr>
      <tr><td>15–29 days</td><td>25% of package cost</td></tr>
      <tr><td>7–14 days</td><td>50% of package cost</td></tr>
      <tr><td>Less than 7 days / no-show</td><td>100% (non-refundable)</td></tr>
    </tbody>
  </table>
</div>

<h2>Frequently Asked Questions</h2>
<div class="cms-accordion">
  <details open>
    <summary>How do I cancel my booking?</summary>
    <p>Go to My Trips in your account and select Cancel Booking. You'll see the exact cancellation charge for your booking before you confirm.</p>
  </details>
  <details>
    <summary>When will I receive my refund?</summary>
    <p>Approved refunds are processed within 7–10 business days to your original payment method.</p>
  </details>
  <details>
    <summary>Are all packages covered by this policy?</summary>
    <p>This is our general policy. Certain fares, promotional packages or supplier terms may carry different (often stricter) cancellation rules, shown at the time of booking.</p>
  </details>
  <details>
    <summary>Can I reschedule instead of cancelling?</summary>
    <p>In many cases, yes — rescheduling can be cheaper than cancelling. Contact support before you cancel to check what's possible for your booking.</p>
  </details>
</div>

<div class="cms-cta">
  <h2>Need to Cancel or Reschedule?</h2>
  <p>Our support team can walk you through your options and the exact charges that apply.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Policy/Legal Page" template — same photo-free hero + callout language as
    // cancellationPolicyBody(), with info-category cards instead of a charges table.
    private function privacyPolicyBody(): string
    {
        return <<<HTML
<section class="cms-hero cms-hero-plain">
  <div class="cms-hero-inner">
    <h1>Privacy Policy</h1>
    <p>Your data, your trust — here's exactly how we handle both.</p>
  </div>
</section>

<p>We respect your privacy. This policy explains what information FareBuzzer collects when you use our website or app, how we use it to provide and improve our services, and the choices you have over your own data.</p>

<div class="cms-callout">
  <p><strong>Last updated:</strong> This policy applies to all users of the FareBuzzer website and app and is reviewed periodically as our services evolve.</p>
</div>

<h2>What We Collect</h2>
<div class="cms-cards">
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-person-badge"></i></div>
    <h5>Personal Details</h5>
    <p>Name, email, phone number and other details you give us when booking or creating an account.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
    <h5>Booking &amp; Payment Info</h5>
    <p>Trip details and payment references — card numbers are processed securely by our payment partners, never stored in full by us.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-laptop"></i></div>
    <h5>Device &amp; Usage Data</h5>
    <p>Browser type, IP address and the pages you visit, used to keep the site fast and secure.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-geo-alt"></i></div>
    <h5>Location Data</h5>
    <p>Only with your permission — used to show nearby deals and relevant destinations.</p>
  </div>
</div>

<h2>Frequently Asked Questions</h2>
<div class="cms-accordion">
  <details open>
    <summary>Do you share my information with third parties?</summary>
    <p>We share only what's needed to complete your booking — for example, with the hotel or airline you're travelling with — and never sell your personal data.</p>
  </details>
  <details>
    <summary>How can I update or delete my data?</summary>
    <p>You can update your details any time from your account, or write to us to request deletion, subject to any records we're legally required to keep.</p>
  </details>
  <details>
    <summary>Is my payment information secure?</summary>
    <p>Yes — payments are processed through PCI-compliant payment partners. We don't store your full card details on our servers.</p>
  </details>
  <details>
    <summary>How do I opt out of marketing emails?</summary>
    <p>Every marketing email includes an unsubscribe link, or you can update your communication preferences from your account settings.</p>
  </details>
</div>

<div class="cms-cta">
  <h2>Questions About Your Privacy?</h2>
  <p>Reach out any time if you'd like to know more about how your data is handled.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Policy/Legal Page" template.
    private function termsOfServiceBody(): string
    {
        return <<<HTML
<section class="cms-hero cms-hero-plain">
  <div class="cms-hero-inner">
    <h1>Terms of Service</h1>
    <p>The ground rules for using FareBuzzer — plain and simple.</p>
  </div>
</section>

<p>By using FareBuzzer, you agree to these terms of service governing bookings, payments, cancellations and use of our website. Please read them alongside our Privacy Policy and the individual Cancellation Policy for your specific booking.</p>

<div class="cms-callout">
  <p><strong>By booking with us,</strong> you confirm you have the authority to accept these terms on behalf of everyone included in your booking.</p>
</div>

<h2>Key Terms</h2>
<div class="cms-cards">
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-wallet2"></i></div>
    <h5>Bookings &amp; Payments</h5>
    <p>Prices are confirmed only once payment is received; some fares may change until then.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
    <h5>Cancellations &amp; Refunds</h5>
    <p>Governed by our Cancellation Policy and the terms of the specific hotel, airline or package.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-shield-check"></i></div>
    <h5>User Responsibilities</h5>
    <p>Accurate traveller details and valid documents are your responsibility at the time of travel.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-exclamation-diamond"></i></div>
    <h5>Limitation of Liability</h5>
    <p>We connect you to third-party suppliers (hotels, airlines) and aren't liable for their independent acts or omissions.</p>
  </div>
  <div class="cms-card">
    <div class="cms-card-icon"><i class="bi bi-arrow-repeat"></i></div>
    <h5>Changes to These Terms</h5>
    <p>We may update these terms from time to time; continued use of FareBuzzer means you accept the current version.</p>
  </div>
</div>

<h2>Frequently Asked Questions</h2>
<div class="cms-accordion">
  <details open>
    <summary>Can FareBuzzer change these terms?</summary>
    <p>Yes, occasionally to reflect new features, legal requirements or policy updates — the latest version always applies from the date it's published.</p>
  </details>
  <details>
    <summary>Are hotels and airlines bound by these terms too?</summary>
    <p>Third-party suppliers operate under their own terms and conditions in addition to ours — these are shown at the time of booking where applicable.</p>
  </details>
  <details>
    <summary>What happens if I provide incorrect traveller details?</summary>
    <p>Suppliers may deny boarding or check-in for mismatched documents, and correction fees may apply — please double-check details before confirming.</p>
  </details>
  <details>
    <summary>Which laws govern this agreement?</summary>
    <p>These terms are governed by applicable Indian law, and any disputes are subject to the jurisdiction of our registered corporate office.</p>
  </details>
</div>

<div class="cms-cta">
  <h2>Questions About These Terms?</h2>
  <p>Our team is happy to clarify anything before you book.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // "Policy/Legal Page" template — a cookie-types table mirrors the charges table on
    // cancellationPolicyBody() for a consistent legal-page feel.
    private function cookiePolicyBody(): string
    {
        return <<<HTML
<section class="cms-hero cms-hero-plain">
  <div class="cms-hero-inner">
    <h1>Cookie Policy</h1>
    <p>How we use cookies to make FareBuzzer work better for you.</p>
  </div>
</section>

<p>FareBuzzer uses cookies to improve your browsing experience, remember your preferences and analyse site traffic. This page explains the types of cookies we use and how you can control them.</p>

<h2>Cookies We Use</h2>
<div class="cms-table-wrap">
  <table>
    <thead>
      <tr><th>Type</th><th>Purpose</th><th>Typical Duration</th></tr>
    </thead>
    <tbody>
      <tr><td>Essential</td><td>Required for login, checkout and core site functionality</td><td>Session</td></tr>
      <tr><td>Performance</td><td>Helps us understand site usage and improve speed</td><td>Up to 1 year</td></tr>
      <tr><td>Functional</td><td>Remembers preferences like currency and language</td><td>Up to 6 months</td></tr>
      <tr><td>Advertising</td><td>Personalises offers and measures campaign performance</td><td>Up to 1 year</td></tr>
    </tbody>
  </table>
</div>

<div class="cms-callout">
  <p><strong>Managing cookies:</strong> You can control or disable cookies any time through your browser settings. Disabling essential cookies may affect how parts of the site work, such as staying logged in or completing a booking.</p>
</div>

<div class="cms-cta">
  <h2>Questions About Cookies?</h2>
  <p>Reach out if you'd like more detail on any of the cookies we use.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }

    // Navigational template, distinct from the legal pages — grouped link cards instead
    // of prose, so it stays genuinely useful as a fallback way to find any page on the site.
    private function sitemapBody(): string
    {
        $home = url('/');
        $india = route('packages.india');
        $intl = route('packages.international');
        $mice = route('packages.mice');
        $activities = route('activities.index');
        $hotelsIdx = route('hotels.index');
        $aboutUs = route('about-us');
        $careers = url('careers');
        $blog = route('blog.index');
        $investors = url('investor-relations');
        $partner = url('partner-with-us');
        $helpCenter = url('help-center');
        $cancellation = url('cancellation-policy');
        $insurance = url('travel-insurance');
        $contactUs = route('contact_us');
        $privacy = url('privacy-policy');
        $terms = url('terms-of-service');
        $cookies = url('cookie-policy');

        return <<<HTML
<section class="cms-hero cms-hero-plain">
  <div class="cms-hero-inner">
    <h1>Sitemap</h1>
    <p>Every part of FareBuzzer, one page away.</p>
  </div>
</section>

<p>An overview of all pages on FareBuzzer — use the groups below to jump straight to what you're looking for.</p>

<div class="cms-cards">
  <div class="cms-card cms-card-links">
    <div class="cms-card-icon"><i class="bi bi-compass"></i></div>
    <h5>Explore</h5>
    <ul>
      <li><a href="{$home}">Home</a></li>
      <li><a href="{$india}">India Packages</a></li>
      <li><a href="{$intl}">International Packages</a></li>
      <li><a href="{$mice}">MICE</a></li>
      <li><a href="{$activities}">Activities</a></li>
      <li><a href="{$hotelsIdx}">Hotels</a></li>
    </ul>
  </div>
  <div class="cms-card cms-card-links">
    <div class="cms-card-icon"><i class="bi bi-building"></i></div>
    <h5>Company</h5>
    <ul>
      <li><a href="{$aboutUs}">About Us</a></li>
      <li><a href="{$careers}">Careers</a></li>
      <li><a href="{$blog}">Blog</a></li>
      <li><a href="{$investors}">Investor Relations</a></li>
      <li><a href="{$partner}">Partner With Us</a></li>
    </ul>
  </div>
  <div class="cms-card cms-card-links">
    <div class="cms-card-icon"><i class="bi bi-headset"></i></div>
    <h5>Support</h5>
    <ul>
      <li><a href="{$helpCenter}">Help Center</a></li>
      <li><a href="{$cancellation}">Cancellation Policy</a></li>
      <li><a href="{$insurance}">Travel Insurance</a></li>
      <li><a href="{$contactUs}">Contact Us</a></li>
    </ul>
  </div>
  <div class="cms-card cms-card-links">
    <div class="cms-card-icon"><i class="bi bi-shield-lock"></i></div>
    <h5>Legal</h5>
    <ul>
      <li><a href="{$privacy}">Privacy Policy</a></li>
      <li><a href="{$terms}">Terms of Service</a></li>
      <li><a href="{$cookies}">Cookie Policy</a></li>
    </ul>
  </div>
</div>

<div class="cms-cta">
  <h2>Can't Find What You're Looking For?</h2>
  <p>Our team is happy to point you in the right direction.</p>
  <a class="cms-btn" href="mailto:enquiry@farebuzzertravel.com">Email enquiry@farebuzzertravel.com</a>
</div>
HTML;
    }
}
