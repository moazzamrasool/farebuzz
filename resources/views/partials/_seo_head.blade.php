{{--
  Emits every @section the layout's <head> reads, from a resolved SeoResolver array.
  Usage in any public page:
    @php $seo = \App\Support\Seo\SeoResolver::resolve($model, 'Fallback title', $descriptionSource, $imagePath, $canonicalUrl); @endphp
    @include('partials._seo_head', ['seo' => $seo])
--}}
@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@if($seo['keywords'])
  @section('meta_keywords', $seo['keywords'])
@endif
@section('canonical', $seo['canonical'])
@section('robots', $seo['robots'])
@section('og_title', $seo['ogTitle'])
@section('og_description', $seo['ogDescription'])
@if($seo['ogImage'])
  @section('og_image', $seo['ogImage'])
@endif
@section('og_url', $seo['canonical'])
@section('twitter_title', $seo['ogTitle'])
@section('twitter_description', $seo['ogDescription'])
@if($seo['ogImage'])
  @section('twitter_image', $seo['ogImage'])
@endif
