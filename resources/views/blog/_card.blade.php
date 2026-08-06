{{-- Blog card. Expects: $blog. --}}
<a href="{{ route('blog.show', $blog->slug) }}" class="blog-card">
  <img class="blog-card-img" loading="lazy"
    src="{{ $blog->featured_image ? asset('storage/'.$blog->featured_image) : 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=700&q=70' }}"
    alt="{{ $blog->title }}">
  <div class="blog-card-body">
    <span class="blog-card-category">{{ $blog->category_label }}</span>
    <h3 class="blog-card-title">{{ $blog->title }}</h3>
    <div class="blog-card-meta">
      <span><i class="bi bi-calendar3"></i> {{ $blog->published_at?->format('d M Y') }}</span>
      <span><i class="bi bi-clock"></i> {{ $blog->read_time }}</span>
    </div>
  </div>
</a>
