
@extends('layouts.admin.app')

@section('content')
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('crm.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        {{-- ══════════ KPI CARDS ══════════ --}}
        <div class="row">
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-blue"><i class="fas fa-suitcase-rolling"></i></span>
              <div class="kpi-number">{{ $kpis['packages'] }}</div>
              <div class="kpi-label">Holiday Packages</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-green"><i class="fas fa-check-circle"></i></span>
              <div class="kpi-number">{{ $kpis['active'] }}</div>
              <div class="kpi-label">Active Packages</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-purple"><i class="fas fa-map-marker-alt"></i></span>
              <div class="kpi-number">{{ $kpis['destinations'] }}</div>
              <div class="kpi-label">Destinations</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-orange"><i class="fas fa-hotel"></i></span>
              <div class="kpi-number">{{ $kpis['hotels'] }}</div>
              <div class="kpi-label">Hotels</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-yellow"><i class="fas fa-star"></i></span>
              <div class="kpi-number">{{ $kpis['avg_rating'] ?? '—' }}</div>
              <div class="kpi-label">Avg. Rating</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-teal"><i class="fas fa-comment-dots"></i></span>
              <div class="kpi-number">{{ $kpis['reviews'] }}</div>
              <div class="kpi-label">Guest Reviews</div>
            </div>
          </div>
        </div>

        {{-- ══════════ CHARTS ══════════ --}}
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Packages by Destination</h3>
              </div>
              <div class="card-body">
                @if($packagesByDestination->isEmpty())
                  <p class="text-muted text-center py-5 mb-0">No packages yet — add a destination and a holiday package to see this chart.</p>
                @else
                  <canvas id="destinationChart" height="90"></canvas>
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Package Status</h3>
              </div>
              <div class="card-body d-flex flex-column align-items-center">
                @if($kpis['packages'] === 0)
                  <p class="text-muted text-center py-5 mb-0">No packages yet.</p>
                @else
                  <canvas id="statusChart" height="170"></canvas>
                  <ul class="chart-legend clearfix mt-3 mb-0">
                    <li><i class="fas fa-circle text-success"></i> Active ({{ $statusBreakdown['active'] }})</li>
                    <li><i class="fas fa-circle text-secondary"></i> Inactive ({{ $statusBreakdown['inactive'] }})</li>
                  </ul>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- ══════════ TOP PACKAGES + RECENT REVIEWS ══════════ --}}
        <div class="row">
          <div class="col-md-7">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Top Performing Packages</h3>
              </div>
              <div class="card-body p-0">
                @if($topPackages->isEmpty())
                  <p class="text-muted text-center py-5 mb-0">No packages yet.</p>
                @else
                  <div class="table-responsive">
                    <table class="table m-0 align-middle">
                      <thead>
                        <tr>
                          <th>Package</th>
                          <th>Destination</th>
                          <th>Price</th>
                          <th>Rating</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($topPackages as $package)
                          @php $coverPhoto = $package->photos->firstWhere('is_cover', true) ?? $package->photos->first(); @endphp
                          <tr>
                            <td>
                              <div class="d-flex align-items-center" style="gap:10px;">
                                @if($coverPhoto)
                                  <img src="{{ asset('storage/'.$coverPhoto->path) }}" alt="" style="width:38px;height:38px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                                @else
                                  <span class="d-flex align-items-center justify-content-center text-muted" style="width:38px;height:38px;border-radius:6px;background:#f3f4f6;flex-shrink:0;"><i class="fas fa-image"></i></span>
                                @endif
                                <div>
                                  <a href="{{ route('crm.holiday-packages.edit', $package->id) }}" class="font-weight-bold text-dark">{{ $package->title }}</a>
                                  @if($package->is_best_seller)
                                    <span class="badge tag-primary ml-1">Best Seller</span>
                                  @endif
                                </div>
                              </div>
                            </td>
                            <td>{{ $package->destination->name ?? '—' }}</td>
                            <td>
                              @if($package->discounted_price)
                                <span class="font-weight-bold">₹{{ number_format($package->discounted_price) }}</span>
                                <small class="text-muted"><s>₹{{ number_format($package->price) }}</s></small>
                              @else
                                <span class="font-weight-bold">₹{{ number_format($package->price) }}</span>
                              @endif
                            </td>
                            <td>
                              @if($package->reviews_avg_rating)
                                <span class="text-warning">@include('admin.partials._stars', ['rating' => $package->reviews_avg_rating])</span>
                                <small class="text-muted d-block">{{ $package->reviews_count }} review{{ $package->reviews_count === 1 ? '' : 's' }}</small>
                              @else
                                <span class="text-muted small">No reviews yet</span>
                              @endif
                            </td>
                            <td>
                              <span class="badge {{ $package->status === 'active' ? 'badge-success' : 'badge-secondary' }}">{{ ucfirst($package->status) }}</span>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>
              <div class="card-footer text-center">
                <a href="{{ route('crm.holiday-packages.index') }}">View All Packages</a>
              </div>
            </div>
          </div>

          <div class="col-md-5">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Recent Reviews</h3>
              </div>
              <div class="card-body p-0">
                @if($recentReviews->isEmpty())
                  <p class="text-muted text-center py-5 mb-0">No reviews yet.</p>
                @else
                  <div class="review-feed">
                    @foreach($recentReviews as $review)
                      <div class="review-feed-item">
                        <span class="crm-avatar-badge">{{ strtoupper(substr($review->reviewer_name, 0, 1)) }}</span>
                        <div class="review-feed-body">
                          <div class="d-flex justify-content-between align-items-start">
                            <strong>{{ $review->reviewer_name }}</strong>
                            <span class="text-warning small">@include('admin.partials._stars', ['rating' => $review->rating])</span>
                          </div>
                          <div class="text-muted small mb-1">
                            {{ $review->holidayPackage->title ?? 'Unknown package' }}
                            &middot; {{ $review->review_date?->format('d M Y') }}
                          </div>
                          @if($review->comment)
                            <p class="mb-0 small">{{ \Illuminate\Support\Str::limit($review->comment, 110) }}</p>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
              <div class="card-footer text-center">
                <a href="{{ route('crm.holiday-packages.index') }}">Manage Reviews</a>
              </div>
            </div>
          </div>
        </div>

        {{-- ══════════ REVIEWS OVER TIME ══════════ --}}
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Reviews Received Over Time</h3>
              </div>
              <div class="card-body">
                @if($reviewsByMonth->isEmpty())
                  <p class="text-muted text-center py-5 mb-0">No reviews yet — this chart fills in as guests leave reviews.</p>
                @else
                  <canvas id="reviewsChart" height="70"></canvas>
                @endif
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  <script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
  <script>
    (function () {
      Chart.defaults.global.defaultFontFamily = 'Inter, -apple-system, sans-serif';
      Chart.defaults.global.defaultFontColor = '#6b7280';

      @if($packagesByDestination->isNotEmpty())
      new Chart(document.getElementById('destinationChart'), {
        type: 'bar',
        data: {
          labels: {!! json_encode($packagesByDestination->pluck('name')) !!},
          datasets: [{
            label: 'Packages',
            data: {!! json_encode($packagesByDestination->pluck('holiday_packages_count')) !!},
            backgroundColor: '#1352cc',
            borderRadius: 4,
            maxBarThickness: 36
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { color: '#f3f4f6' } }],
            xAxes: [{ gridLines: { display: false } }]
          }
        }
      });
      @endif

      @if($kpis['packages'] > 0)
      new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
          labels: ['Active', 'Inactive'],
          datasets: [{
            data: [{{ $statusBreakdown['active'] }}, {{ $statusBreakdown['inactive'] }}],
            backgroundColor: ['#16a34a', '#d1d5db'],
            borderWidth: 0
          }]
        },
        options: {
          legend: { display: false },
          cutoutPercentage: 68
        }
      });
      @endif

      @if($reviewsByMonth->isNotEmpty())
      new Chart(document.getElementById('reviewsChart'), {
        type: 'line',
        data: {
          labels: {!! json_encode($reviewsByMonth->pluck('label')) !!},
          datasets: [{
            label: 'Reviews',
            data: {!! json_encode($reviewsByMonth->pluck('total')) !!},
            borderColor: '#1352cc',
            backgroundColor: 'rgba(19,82,204,0.08)',
            pointBackgroundColor: '#1352cc',
            fill: true,
            tension: 0.35
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { color: '#f3f4f6' } }],
            xAxes: [{ gridLines: { display: false } }]
          }
        }
      });
      @endif
    })();
  </script>
@endsection
