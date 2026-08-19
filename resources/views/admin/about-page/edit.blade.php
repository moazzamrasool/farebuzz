@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">About Us Page</h3>
                <div class="card-tools">
                  <a href="{{ url('/about-us') }}" target="_blank" class="btn btn-secondary btn-sm">Preview</a>
                </div>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.about-page.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <h5 class="mb-3">Hero</h5>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="hero_heading">Heading</label>
                      <input type="text" name="hero_heading" id="hero_heading" class="form-control @error('hero_heading') is-invalid @enderror"
                        value="{{ old('hero_heading', $about->hero_heading) }}" placeholder="Personalised, Once In A Lifetime Trips">
                      @error('hero_heading') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-6">
                      <label for="hero_tagline">Tagline</label>
                      <input type="text" name="hero_tagline" id="hero_tagline" class="form-control @error('hero_tagline') is-invalid @enderror"
                        value="{{ old('hero_tagline', $about->hero_tagline) }}">
                      @error('hero_tagline') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="hero_image">Hero Image</label>
                    <input type="file" name="hero_image" id="hero_image" class="form-control-file @error('hero_image') is-invalid @enderror" accept="image/*">
                    @error('hero_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    @if($about->hero_image)
                      <div class="mt-2"><img src="{{ \App\Support\MediaUrl::resolve($about->hero_image) }}" style="width:160px;height:100px;object-fit:cover;border-radius:6px;"></div>
                    @endif
                  </div>

                  <hr>
                  <h5 class="mb-3">Our Story</h5>
                  <div class="form-group">
                    <label for="story_heading">Heading</label>
                    <input type="text" name="story_heading" id="story_heading" class="form-control" value="{{ old('story_heading', $about->story_heading) }}" placeholder="Our Story">
                  </div>
                  <div class="form-group">
                    <label for="story_image">Image</label>
                    <input type="file" name="story_image" id="story_image" class="form-control-file @error('story_image') is-invalid @enderror" accept="image/*">
                    @error('story_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    @if($about->story_image)
                      <div class="mt-2"><img src="{{ \App\Support\MediaUrl::resolve($about->story_image) }}" style="width:160px;height:100px;object-fit:cover;border-radius:6px;"></div>
                    @endif
                  </div>
                  <div class="form-group">
                    <label for="story_body">Text</label>
                    <textarea name="story_body" id="story_body" rows="8" class="form-control rich-text-editor" data-editor-height="300">{{ old('story_body', $about->story_body) }}</textarea>
                  </div>

                  <hr>
                  <h5 class="mb-3">Our Mission</h5>
                  <div class="form-group">
                    <label for="mission_heading">Heading</label>
                    <input type="text" name="mission_heading" id="mission_heading" class="form-control" value="{{ old('mission_heading', $about->mission_heading) }}" placeholder="Our Mission">
                  </div>
                  <div class="form-group">
                    <label for="mission_image">Image</label>
                    <input type="file" name="mission_image" id="mission_image" class="form-control-file @error('mission_image') is-invalid @enderror" accept="image/*">
                    @error('mission_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    @if($about->mission_image)
                      <div class="mt-2"><img src="{{ \App\Support\MediaUrl::resolve($about->mission_image) }}" style="width:160px;height:100px;object-fit:cover;border-radius:6px;"></div>
                    @endif
                  </div>
                  <div class="form-group">
                    <label for="mission_body">Text</label>
                    <textarea name="mission_body" id="mission_body" rows="8" class="form-control rich-text-editor" data-editor-height="300">{{ old('mission_body', $about->mission_body) }}</textarea>
                  </div>
                  <h6 class="mt-3">Mission Cards</h6>
                  @include('admin.about-page._repeater', ['sectionKey' => 'mission_card', 'addLabel' => 'Add Card', 'titleLabel' => 'Title', 'subtitleLabel' => 'Icon (e.g. bi-compass)', 'showDescription' => true, 'showImage' => false])

                  <h6 class="mt-4">The Talent Behind Every Journey</h6>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="team_heading">Heading</label>
                      <input type="text" name="team_heading" id="team_heading" class="form-control" value="{{ old('team_heading', $about->team_heading) }}" placeholder="The Talent Behind Every Journey">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="team_subheading">Subheading</label>
                      <input type="text" name="team_subheading" id="team_subheading" class="form-control" value="{{ old('team_subheading', $about->team_subheading) }}">
                    </div>
                  </div>
                  @include('admin.about-page._repeater', ['sectionKey' => 'team_member', 'addLabel' => 'Add Team Member', 'titleLabel' => 'Name', 'subtitleLabel' => 'Role'])

                  <hr>
                  <h5 class="mb-1">Why Travel With Us</h5>
                  <p class="text-muted" style="font-size:13px;">Icon field takes a <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Bootstrap Icons</a> class, e.g. <code>bi-shield-check</code>.</p>
                  @for($i = 1; $i <= 3; $i++)
                    <div class="form-row border rounded p-2 mb-2 mx-0">
                      <div class="form-group col-md-2">
                        <label for="feature{{ $i }}_icon">Icon</label>
                        <input type="text" name="feature{{ $i }}_icon" id="feature{{ $i }}_icon" class="form-control"
                          value="{{ old("feature{$i}_icon", $about->{"feature{$i}_icon"}) }}" placeholder="bi-shield-check">
                      </div>
                      <div class="form-group col-md-4">
                        <label for="feature{{ $i }}_title">Title</label>
                        <input type="text" name="feature{{ $i }}_title" id="feature{{ $i }}_title" class="form-control"
                          value="{{ old("feature{$i}_title", $about->{"feature{$i}_title"}) }}" placeholder="e.g. Trusted Partners">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="feature{{ $i }}_description">Description</label>
                        <input type="text" name="feature{{ $i }}_description" id="feature{{ $i }}_description" class="form-control"
                          value="{{ old("feature{$i}_description", $about->{"feature{$i}_description"}) }}" placeholder="Short description">
                      </div>
                    </div>
                  @endfor

                  <hr>
                  <h5 class="mb-1">We Are Growing!</h5>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="growth_heading">Heading</label>
                      <input type="text" name="growth_heading" id="growth_heading" class="form-control" value="{{ old('growth_heading', $about->growth_heading) }}" placeholder="We Are Growing!">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="growth_subheading">Subheading</label>
                      <input type="text" name="growth_subheading" id="growth_subheading" class="form-control" value="{{ old('growth_subheading', $about->growth_subheading) }}">
                    </div>
                  </div>
                  <p class="text-muted" style="font-size:13px;">Title = the bar's percentage/value (e.g. "48%"), Subtitle = the year (e.g. "2024").</p>
                  @include('admin.about-page._repeater', ['sectionKey' => 'growth_stat', 'addLabel' => 'Add Bar', 'titleLabel' => 'Value (e.g. 48%)', 'subtitleLabel' => 'Year', 'showImage' => false])

                  <hr>
                  <h5 class="mb-1">Journey Timeline</h5>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="timeline_heading">Heading</label>
                      <input type="text" name="timeline_heading" id="timeline_heading" class="form-control" value="{{ old('timeline_heading', $about->timeline_heading) }}" placeholder="Journey That Made FareBuzzer">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="timeline_subheading">Subheading</label>
                      <input type="text" name="timeline_subheading" id="timeline_subheading" class="form-control" value="{{ old('timeline_subheading', $about->timeline_subheading) }}">
                    </div>
                  </div>
                  <p class="text-muted" style="font-size:13px;">Title = milestone heading, Subtitle = year, Description = short blurb. Shown in the order listed below.</p>
                  @include('admin.about-page._repeater', ['sectionKey' => 'timeline', 'addLabel' => 'Add Milestone', 'titleLabel' => 'Milestone', 'subtitleLabel' => 'Year', 'showDescription' => true])

                  <hr>
                  <h5 class="mb-3">Life At FareBuzzer</h5>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="life_heading">Heading</label>
                      <input type="text" name="life_heading" id="life_heading" class="form-control" value="{{ old('life_heading', $about->life_heading) }}" placeholder="Life At FareBuzzer">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="life_subheading">Subheading</label>
                      <input type="text" name="life_subheading" id="life_subheading" class="form-control" value="{{ old('life_subheading', $about->life_subheading) }}">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="life_image">Image</label>
                    <input type="file" name="life_image" id="life_image" class="form-control-file @error('life_image') is-invalid @enderror" accept="image/*">
                    @error('life_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    @if($about->life_image)
                      <div class="mt-2"><img src="{{ \App\Support\MediaUrl::resolve($about->life_image) }}" style="width:160px;height:100px;object-fit:cover;border-radius:6px;"></div>
                    @endif
                  </div>
                  <div class="form-group">
                    <label for="life_body">Text</label>
                    <textarea name="life_body" id="life_body" rows="4" class="form-control">{{ old('life_body', $about->life_body) }}</textarea>
                  </div>

                  <hr>
                  <h5 class="mb-1">Photo Gallery</h5>
                  <div class="form-group">
                    <label for="gallery_heading">Heading</label>
                    <input type="text" name="gallery_heading" id="gallery_heading" class="form-control" value="{{ old('gallery_heading', $about->gallery_heading) }}" placeholder="FareBuzzer Picture Gallery">
                  </div>
                  @include('admin.about-page._repeater', ['sectionKey' => 'gallery', 'addLabel' => 'Add Photo', 'titleLabel' => 'Caption (optional)'])

                  <hr>
                  <h5 class="mb-1">Voices of FareBuzzer</h5>
                  <div class="form-group">
                    <label for="testimonials_heading">Heading</label>
                    <input type="text" name="testimonials_heading" id="testimonials_heading" class="form-control" value="{{ old('testimonials_heading', $about->testimonials_heading) }}" placeholder="Voices of FareBuzzer">
                  </div>
                  @include('admin.about-page._repeater', ['sectionKey' => 'testimonial', 'addLabel' => 'Add Testimonial', 'titleLabel' => 'Name', 'subtitleLabel' => 'Role', 'showDescription' => true, 'descriptionLabel' => 'Quote'])

                  <hr>
                  <h5 class="mb-1">In The Spotlight</h5>
                  <div class="form-group">
                    <label for="press_heading">Heading</label>
                    <input type="text" name="press_heading" id="press_heading" class="form-control" value="{{ old('press_heading', $about->press_heading) }}" placeholder="In The Spotlight">
                  </div>
                  @include('admin.about-page._repeater', ['sectionKey' => 'press', 'addLabel' => 'Add Press Logo', 'titleLabel' => 'Publication Name', 'showLink' => true])

                  <hr>
                  <h5 class="mb-3">Stories of Impact</h5>
                  <div class="form-group">
                    <label for="impact_heading">Heading</label>
                    <input type="text" name="impact_heading" id="impact_heading" class="form-control" value="{{ old('impact_heading', $about->impact_heading) }}" placeholder="Stories of Impact">
                  </div>
                  <div class="form-group">
                    <label for="impact_image">Image</label>
                    <input type="file" name="impact_image" id="impact_image" class="form-control-file @error('impact_image') is-invalid @enderror" accept="image/*">
                    @error('impact_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    @if($about->impact_image)
                      <div class="mt-2"><img src="{{ \App\Support\MediaUrl::resolve($about->impact_image) }}" style="width:160px;height:100px;object-fit:cover;border-radius:6px;"></div>
                    @endif
                  </div>
                  <div class="form-group">
                    <label for="impact_body">Text</label>
                    <textarea name="impact_body" id="impact_body" rows="4" class="form-control">{{ old('impact_body', $about->impact_body) }}</textarea>
                  </div>

                  <hr>
                  <h5 class="mb-1">Awards & Recognition</h5>
                  <div class="form-group">
                    <label for="awards_heading">Heading</label>
                    <input type="text" name="awards_heading" id="awards_heading" class="form-control" value="{{ old('awards_heading', $about->awards_heading) }}" placeholder="Awards & Recognition">
                  </div>
                  @include('admin.about-page._repeater', ['sectionKey' => 'award', 'addLabel' => 'Add Award', 'titleLabel' => 'Alt Text'])

                  @include('admin.partials._seo_fields', [
                    'seo' => $about,
                    'seoUrl' => route('about-us'),
                    'seoPreviewFallback' => 'About Us – FareBuzzer',
                  ])

                  <button type="submit" class="btn btn-primary">Save Changes</button>
                  <a href="{{ url('/about-us') }}" target="_blank" class="btn btn-secondary">Preview</a>
                </form>
              </div>
            </div>

          </div>
        </div>
        </div>
    </div>
</div>
@endsection
