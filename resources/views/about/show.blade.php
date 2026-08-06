@extends('layouts.app')

@section('title', $about->meta_title ?: 'About Us – FareBuzzer')
@section('meta_description', $about->meta_description ?: $about->hero_tagline)

@push('styles')
<style>
  :root { --blue: #005fcc; --orange: #f47b20; --navy1: #050e24; --navy2: #0a1d4a; --navy3: #0d2d73; }
  body { font-family: 'Inter', sans-serif; }

  .about-eyebrow { color: var(--orange); font-weight: 700; }

  /* Hero */
  .about-hero { background: #f5f8ff; padding: 64px 0; }
  .about-hero h1 { font-size: 40px; font-weight: 800; color: #111; margin-bottom: 14px; line-height: 1.25; }
  .about-hero p { font-size: 17px; color: #555; max-width: 480px; }
  .about-hero-img { width: 100%; height: 340px; object-fit: cover; border-radius: 16px; }

  /* Scale / stats */
  .about-scale { background: linear-gradient(135deg, var(--navy1) 0%, var(--navy2) 55%, var(--navy3) 100%); padding: 56px 0 40px; }
  .about-scale h2 { color: #fff; font-size: 28px; font-weight: 800; text-align: center; margin-bottom: 4px; }
  .about-scale .sub { color: rgba(255,255,255,0.7); text-align: center; margin-bottom: 32px; font-size: 14px; }
  .about-stat-num { font-size: 34px; font-weight: 800; color: #fff; }
  .about-stat-label { font-size: 14px; color: rgba(255,255,255,0.75); margin-top: 4px; }
  .about-badges { margin-top: 40px; padding-top: 28px; border-top: 1px solid rgba(255,255,255,0.12); display: flex; flex-wrap: wrap; gap: 28px; align-items: center; justify-content: center; }
  .about-badges img { height: 40px; max-width: 130px; object-fit: contain; filter: grayscale(1) brightness(2); opacity: .85; }
  .about-badges a { display: inline-flex; }

  /* Split sections (story / mission / life / impact) */
  .about-split { padding: 56px 0; }
  .about-split-img { width: 100%; height: 320px; object-fit: cover; border-radius: 14px; }
  .about-split h2 { font-size: 28px; font-weight: 800; color: #111; margin-bottom: 16px; }
  .about-split .ck-content, .about-split .plain-text { color: #555; font-size: 15px; line-height: 1.7; }

  /* Mission cards */
  .mission-cards { margin-top: 8px; }
  .mission-card { text-align: center; padding: 26px 20px; border-radius: 12px; background: #f8f9fb; height: 100%; }
  .mission-card .icon { width: 52px; height: 52px; border-radius: 50%; background: #dbeafe; color: var(--blue); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 22px; }
  .mission-card h5 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
  .mission-card p { font-size: 13px; color: #666; margin: 0; }

  /* Team */
  .about-team { padding: 8px 0 56px; text-align: center; }
  .about-team h2 { font-size: 26px; font-weight: 800; color: #111; }
  .about-team .sub { color: #777; margin-bottom: 32px; font-size: 14px; }
  .team-member { text-align: center; }
  .team-member img { width: 92px; height: 92px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid #f0f2f5; }
  .team-member h6 { font-size: 14px; font-weight: 700; margin-bottom: 2px; }
  .team-member span { font-size: 12px; color: #888; }

  /* Why */
  .about-why { background: #fff; padding: 64px 0; }
  .about-why h2 { font-size: 28px; font-weight: 800; color: #111; text-align: center; margin-bottom: 32px; }
  .why-card { text-align: center; padding: 32px 24px; border-radius: 12px; border: 1.5px solid #eee; transition: border-color .2s, box-shadow .2s; height: 100%; }
  .why-card:hover { border-color: var(--blue); box-shadow: 0 4px 16px rgba(0,95,204,0.1); }
  .why-icon { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px; background: #dbeafe; color: var(--blue); }
  .why-card h5 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
  .why-card p { font-size: 13px; color: #666; margin: 0; }

  /* Growth chart */
  .about-growth { background: #f8f9fb; padding: 56px 0; text-align: center; }
  .about-growth h2 { font-size: 28px; font-weight: 800; color: #111; }
  .about-growth .sub { color: #777; margin-bottom: 32px; font-size: 14px; }
  .growth-chart { display: flex; align-items: flex-end; justify-content: center; gap: 22px; height: 220px; max-width: 720px; margin: 0 auto; padding: 0 12px; }
  .growth-bar-wrap { display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; flex: 1; max-width: 90px; }
  .growth-bar-val { font-weight: 700; font-size: 13px; color: #111; margin-bottom: 6px; }
  .growth-bar { width: 100%; max-width: 46px; background: linear-gradient(180deg, var(--orange), #ffb066); border-radius: 8px 8px 0 0; }
  .growth-bar-year { margin-top: 8px; font-size: 12px; color: #888; }

  /* Timeline */
  .about-timeline { padding: 56px 0; text-align: center; }
  .about-timeline h2 { font-size: 28px; font-weight: 800; color: #111; }
  .about-timeline .sub { color: #777; margin-bottom: 40px; font-size: 14px; }
  .timeline-list { position: relative; max-width: 900px; margin: 0 auto; text-align: left; }
  .timeline-list::before { content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: #e5e9f0; transform: translateX(-50%); }
  .timeline-row { position: relative; display: flex; align-items: center; margin-bottom: 36px; }
  .timeline-row .timeline-card { width: calc(50% - 32px); background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
  .timeline-row .timeline-year { display: inline-block; background: var(--orange); color: #fff; font-size: 12px; font-weight: 700; padding: 3px 12px; border-radius: 20px; margin-bottom: 8px; }
  .timeline-row .timeline-card h6 { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
  .timeline-row .timeline-card p { font-size: 13px; color: #666; margin: 0; }
  .timeline-row .timeline-card img { width: 100%; height: 130px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
  .timeline-row .timeline-dot { position: absolute; left: 50%; width: 14px; height: 14px; border-radius: 50%; background: var(--blue); border: 3px solid #fff; box-shadow: 0 0 0 2px var(--blue); transform: translateX(-50%); }
  .timeline-row.left { justify-content: flex-start; }
  .timeline-row.right { justify-content: flex-end; }
  @media(max-width:767px) {
    .timeline-list::before { left: 18px; }
    .timeline-row, .timeline-row.left, .timeline-row.right { justify-content: flex-start; padding-left: 40px; }
    .timeline-row .timeline-card { width: 100%; }
    .timeline-row .timeline-dot { left: 18px; }
  }

  /* Gallery */
  .about-gallery { background: #f8f9fb; padding: 56px 0; }
  .about-gallery h2 { font-size: 28px; font-weight: 800; color: #111; text-align: center; margin-bottom: 32px; }
  .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
  .gallery-grid img { width: 100%; height: 160px; object-fit: cover; border-radius: 10px; }
  @media(max-width:767px) { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }

  /* Testimonials */
  .about-testimonials { padding: 56px 0; }
  .about-testimonials h2 { font-size: 28px; font-weight: 800; color: #111; text-align: center; margin-bottom: 32px; }
  .testimonial-card { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 22px; height: 100%; }
  .testimonial-card p { font-size: 14px; color: #555; font-style: italic; margin-bottom: 16px; }
  .testimonial-person { display: flex; align-items: center; gap: 10px; }
  .testimonial-person img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
  .testimonial-person h6 { font-size: 14px; font-weight: 700; margin: 0; }
  .testimonial-person span { font-size: 12px; color: #888; }

  /* Press */
  .about-press { background: #fff; padding: 48px 0; text-align: center; }
  .about-press h2 { font-size: 22px; font-weight: 800; color: #111; margin-bottom: 28px; }
  .press-strip { display: flex; flex-wrap: wrap; gap: 32px; align-items: center; justify-content: center; }
  .press-strip img { height: 34px; max-width: 130px; object-fit: contain; filter: grayscale(1); opacity: .7; transition: opacity .2s, filter .2s; }
  .press-strip a:hover img { filter: none; opacity: 1; }

  /* Awards */
  .about-awards { background: #f8f9fb; padding: 48px 0; text-align: center; }
  .about-awards h2 { font-size: 22px; font-weight: 800; color: #111; margin-bottom: 28px; }
  .awards-strip { display: flex; flex-wrap: wrap; gap: 28px; align-items: center; justify-content: center; }
  .awards-strip img { height: 70px; width: 70px; object-fit: contain; }

  /* CTA banners */
  .about-cta { padding: 52px 0; text-align: center; }
  .about-cta h2 { font-size: 28px; font-weight: 800; margin-bottom: 10px; }
  .about-cta p { margin-bottom: 22px; }
  .about-cta .btn { font-weight: 700; padding: 12px 32px; border-radius: 30px; border: none; }
  .about-cta.cta-orange { background: var(--orange); }
  .about-cta.cta-orange h2, .about-cta.cta-orange p { color: #fff; }
  .about-cta.cta-orange p { color: rgba(255,255,255,0.9); }
  .about-cta.cta-orange .btn { background: #fff; color: var(--orange); }
  .about-cta.cta-orange .btn:hover { background: #111; color: #fff; }
  .about-cta.cta-navy { background: linear-gradient(135deg, var(--navy1) 0%, var(--navy2) 55%, var(--navy3) 100%); }
  .about-cta.cta-navy h2 { color: #fff; }
  .about-cta.cta-navy p { color: rgba(255,255,255,0.75); }
  .about-cta.cta-navy .btn { background: var(--orange); color: #fff; }
  .about-cta.cta-navy .btn:hover { background: #fff; color: var(--orange); }

  @media(max-width:767px) {
    .about-hero { padding: 44px 0; text-align: center; }
    .about-hero h1 { font-size: 28px; }
    .about-hero p { margin: 0 auto; }
    .about-hero-img { height: 220px; margin-top: 24px; }
    .about-split { padding: 36px 0; }
    .about-split-img { height: 220px; margin-bottom: 24px; }
    .about-scale, .about-why, .about-growth, .about-timeline, .about-gallery, .about-testimonials, .about-press, .about-awards, .about-cta { padding: 36px 0; }
    .growth-chart { gap: 10px; }
  }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="about-hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-12 col-md-6">
        <h1>{{ $about->hero_heading ?: 'About FareBuzzer' }}</h1>
        <p>{{ $about->hero_tagline ?: 'Founded to make travel planning effortless, personal, and reliable.' }}</p>
      </div>
      <div class="col-12 col-md-6">
        <img class="about-hero-img" src="{{ \App\Support\MediaUrl::resolve($about->hero_image) ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=900&q=80' }}" alt="{{ $about->hero_heading }}">
      </div>
    </div>
  </div>
</section>

{{-- Scale / stats --}}
@php $stats = $about->resolvedStats(); @endphp
@if(count($stats) || $items['trust_badge']->isNotEmpty())
<section class="about-scale">
  <div class="container">
    @if($about->scale_heading)<h2>{{ $about->scale_heading }}</h2>@endif
    @if($about->scale_subheading)<div class="sub">{{ $about->scale_subheading }}</div>@endif
    @if(count($stats))
      <div class="row g-4 text-center">
        @foreach($stats as $stat)
          <div class="col-6 col-md-3">
            <div class="about-stat-num">{{ $stat['value'] }}</div>
            <div class="about-stat-label">{{ $stat['label'] }}</div>
          </div>
        @endforeach
      </div>
    @endif
    @if($items['trust_badge']->isNotEmpty())
      <div class="about-badges">
        @foreach($items['trust_badge'] as $badge)
          @if($badge->link)
            <a href="{{ $badge->link }}" target="_blank" rel="noopener"><img src="{{ \App\Support\MediaUrl::resolve($badge->image) }}" alt="{{ $badge->title }}"></a>
          @else
            <img src="{{ \App\Support\MediaUrl::resolve($badge->image) }}" alt="{{ $badge->title }}">
          @endif
        @endforeach
      </div>
    @endif
  </div>
</section>
@endif

{{-- Our Story --}}
@if($about->story_heading || $about->story_body)
<section class="about-split">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-12 col-md-6">
        <img class="about-split-img" src="{{ \App\Support\MediaUrl::resolve($about->story_image) ?: 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=900&q=80' }}" alt="{{ $about->story_heading }}">
      </div>
      <div class="col-12 col-md-6">
        <h2>{{ $about->story_heading ?: 'Our Story' }}</h2>
        <div class="ck-content">{!! $about->story_body !!}</div>
      </div>
    </div>
  </div>
</section>
@endif

{{-- Our Mission + mission cards + team --}}
@if($about->mission_heading || $about->mission_body)
<section class="about-split" style="background:#f8f9fb;padding-bottom:16px;">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-12 col-md-6 order-md-2">
        <img class="about-split-img" src="{{ \App\Support\MediaUrl::resolve($about->mission_image) ?: 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=900&q=80' }}" alt="{{ $about->mission_heading }}">
      </div>
      <div class="col-12 col-md-6 order-md-1">
        <h2>{{ $about->mission_heading ?: 'Our Mission' }}</h2>
        <div class="ck-content">{!! $about->mission_body !!}</div>
      </div>
    </div>

    @if($items['mission_card']->isNotEmpty())
      <div class="row g-3 mission-cards">
        @foreach($items['mission_card'] as $card)
          <div class="col-6 col-md-3">
            <div class="mission-card">
              <div class="icon"><i class="bi {{ $card->subtitle ?: 'bi-star' }}"></i></div>
              <h5>{{ $card->title }}</h5>
              <p>{{ $card->description }}</p>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>

@if($items['team_member']->isNotEmpty())
<section class="about-team" style="background:#f8f9fb;">
  <div class="container">
    @if($about->team_heading)<h2>{{ $about->team_heading }}</h2>@endif
    @if($about->team_subheading)<div class="sub">{{ $about->team_subheading }}</div>@endif
    <div class="row g-4 justify-content-center">
      @foreach($items['team_member'] as $member)
        <div class="col-6 col-md-2">
          <div class="team-member">
            <img src="{{ \App\Support\MediaUrl::resolve($member->image) ?: 'https://ui-avatars.com/api/?name='.urlencode($member->title ?: '?').'&background=005fcc&color=fff' }}" alt="{{ $member->title }}">
            <h6>{{ $member->title }}</h6>
            <span>{{ $member->subtitle }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif
@endif

{{-- Why Travel With Us --}}
@php $features = $about->resolvedFeatures(); @endphp
@if(count($features))
<section class="about-why">
  <div class="container">
    <h2>Why Travel With Us</h2>
    <div class="row g-4">
      @foreach($features as $feature)
        <div class="col-12 col-md-4">
          <div class="why-card">
            <div class="why-icon"><i class="bi {{ $feature['icon'] }}"></i></div>
            <h5>{{ $feature['title'] }}</h5>
            <p>{{ $feature['description'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- We Are Growing --}}
@if($items['growth_stat']->isNotEmpty())
@php
  $maxPercent = $items['growth_stat']->map(fn($s) => (float) preg_replace('/[^0-9.]/', '', (string) $s->title))->max() ?: 1;
@endphp
<section class="about-growth">
  <div class="container">
    <h2>{{ $about->growth_heading ?: 'We Are Growing!' }}</h2>
    @if($about->growth_subheading)<div class="sub">{{ $about->growth_subheading }}</div>@endif
    <div class="growth-chart">
      @foreach($items['growth_stat'] as $stat)
        @php $numeric = (float) preg_replace('/[^0-9.]/', '', (string) $stat->title); @endphp
        <div class="growth-bar-wrap">
          <div class="growth-bar-val">{{ $stat->title }}</div>
          <div class="growth-bar" style="height:{{ $maxPercent > 0 ? max(8, ($numeric / $maxPercent) * 100) : 8 }}%"></div>
          <div class="growth-bar-year">{{ $stat->subtitle }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Timeline --}}
@if($items['timeline']->isNotEmpty())
<section class="about-timeline">
  <div class="container">
    <h2>{{ $about->timeline_heading ?: 'Our Journey' }}</h2>
    @if($about->timeline_subheading)<div class="sub">{{ $about->timeline_subheading }}</div>@endif
    <div class="timeline-list">
      @foreach($items['timeline'] as $i => $milestone)
        <div class="timeline-row {{ $i % 2 === 0 ? 'left' : 'right' }}">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            @if($milestone->image)
              <img src="{{ \App\Support\MediaUrl::resolve($milestone->image) }}" alt="{{ $milestone->title }}">
            @endif
            @if($milestone->subtitle)<span class="timeline-year">{{ $milestone->subtitle }}</span>@endif
            <h6>{{ $milestone->title }}</h6>
            <p>{{ $milestone->description }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Life At --}}
@if($about->life_heading || $about->life_body)
<section class="about-split" style="background:#f8f9fb;">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-12 col-md-6">
        <img class="about-split-img" src="{{ \App\Support\MediaUrl::resolve($about->life_image) ?: 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=900&q=80' }}" alt="{{ $about->life_heading }}">
      </div>
      <div class="col-12 col-md-6">
        <h2>{{ $about->life_heading ?: 'Life At FareBuzzer' }}</h2>
        @if($about->life_subheading)<p class="plain-text mb-2"><strong>{{ $about->life_subheading }}</strong></p>@endif
        <p class="plain-text">{{ $about->life_body }}</p>
      </div>
    </div>
  </div>
</section>
@endif

{{-- Gallery --}}
@if($items['gallery']->isNotEmpty())
<section class="about-gallery">
  <div class="container">
    <h2>{{ $about->gallery_heading ?: 'Picture Gallery' }}</h2>
    <div class="gallery-grid">
      @foreach($items['gallery'] as $photo)
        <img src="{{ \App\Support\MediaUrl::resolve($photo->image) }}" alt="{{ $photo->title }}">
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Testimonials --}}
@if($items['testimonial']->isNotEmpty())
<section class="about-testimonials">
  <div class="container">
    <h2>{{ $about->testimonials_heading ?: 'Voices of FareBuzzer' }}</h2>
    <div class="row g-4">
      @foreach($items['testimonial'] as $testimonial)
        <div class="col-12 col-md-4">
          <div class="testimonial-card">
            <p>&ldquo;{{ $testimonial->description }}&rdquo;</p>
            <div class="testimonial-person">
              @if($testimonial->image)
                <img src="{{ \App\Support\MediaUrl::resolve($testimonial->image) }}" alt="{{ $testimonial->title }}">
              @endif
              <div>
                <h6>{{ $testimonial->title }}</h6>
                <span>{{ $testimonial->subtitle }}</span>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Press --}}
@if($items['press']->isNotEmpty())
<section class="about-press">
  <div class="container">
    <h2>{{ $about->press_heading ?: 'In The Spotlight' }}</h2>
    <div class="press-strip">
      @foreach($items['press'] as $press)
        @if($press->link)
          <a href="{{ $press->link }}" target="_blank" rel="noopener"><img src="{{ \App\Support\MediaUrl::resolve($press->image) }}" alt="{{ $press->title }}"></a>
        @else
          <img src="{{ \App\Support\MediaUrl::resolve($press->image) }}" alt="{{ $press->title }}">
        @endif
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Impact --}}
@if($about->impact_heading || $about->impact_body)
<section class="about-split">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-12 col-md-6">
        <img class="about-split-img" src="{{ \App\Support\MediaUrl::resolve($about->impact_image) ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=900&q=80' }}" alt="{{ $about->impact_heading }}">
      </div>
      <div class="col-12 col-md-6">
        <h2>{{ $about->impact_heading ?: 'Stories of Impact' }}</h2>
        <p class="plain-text">{{ $about->impact_body }}</p>
      </div>
    </div>
  </div>
</section>
@endif

{{-- Awards --}}
@if($items['award']->isNotEmpty())
<section class="about-awards">
  <div class="container">
    <h2>{{ $about->awards_heading ?: 'Awards & Recognition' }}</h2>
    <div class="awards-strip">
      @foreach($items['award'] as $award)
        <img src="{{ \App\Support\MediaUrl::resolve($award->image) }}" alt="{{ $award->title }}">
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Primary CTA --}}
<section class="about-cta cta-orange">
  <div class="container">
    <h2>{{ $about->cta_heading ?: 'Ready for your next trip?' }}</h2>
    @if($about->cta_text)<p>{{ $about->cta_text }}</p>@endif
    <a href="{{ $about->cta_button_link ?: route('packages.india') }}" class="btn">{{ $about->cta_button_text ?: 'Explore Packages' }}</a>
  </div>
</section>

{{-- Secondary CTA --}}
@if($about->cta2_heading)
<section class="about-cta cta-navy">
  <div class="container">
    <h2>{{ $about->cta2_heading }}</h2>
    @if($about->cta2_text)<p>{{ $about->cta2_text }}</p>@endif
    @if($about->cta2_button_text)
      <a href="{{ $about->cta2_button_link ?: '#' }}" class="btn">{{ $about->cta2_button_text }}</a>
    @endif
  </div>
</section>
@endif

{{-- Career banner --}}
@if($about->career_heading)
<section class="about-cta cta-navy">
  <div class="container">
    <h2>{{ $about->career_heading }}</h2>
    @if($about->career_text)<p>{{ $about->career_text }}</p>@endif
    @if($about->career_button_text)
      <a href="{{ $about->career_button_link ?: '/careers' }}" class="btn">{{ $about->career_button_text }}</a>
    @endif
  </div>
</section>
@endif

@endsection
