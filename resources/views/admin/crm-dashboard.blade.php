@extends('layouts.admin.app')

@section('content')
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">CRM Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('crm.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('crm.package-enquiries.index') }}">Leads</a></li>
              <li class="breadcrumb-item active">CRM Dashboard</li>
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
              <span class="kpi-icon bg-blue"><i class="fas fa-address-card"></i></span>
              <div class="kpi-number">{{ $totalLeads }}</div>
              <div class="kpi-label">Total Leads</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-green"><i class="fas fa-trophy"></i></span>
              <div class="kpi-number">{{ $convertedCount }}</div>
              <div class="kpi-label">Converted (Won)</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-red"><i class="fas fa-times-circle"></i></span>
              <div class="kpi-number">{{ $lostCount }}</div>
              <div class="kpi-label">Lost / Junk</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-yellow"><i class="fas fa-bell"></i></span>
              <div class="kpi-number">{{ $followUpsDueToday }}</div>
              <div class="kpi-label">Due Today</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-orange"><i class="fas fa-exclamation-triangle"></i></span>
              <div class="kpi-number">{{ $followUpsOverdue }}</div>
              <div class="kpi-label">Overdue</div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card">
              <span class="kpi-icon bg-purple"><i class="fas fa-percentage"></i></span>
              <div class="kpi-number">{{ $totalLeads ? round($convertedCount / $totalLeads * 100) : 0 }}%</div>
              <div class="kpi-label">Conversion Rate</div>
            </div>
          </div>
        </div>

        <div class="row">
          {{-- ══════════ STATUS BREAKDOWN ══════════ --}}
          <div class="col-md-5">
            <div class="card">
              <div class="card-header"><h3 class="card-title">Leads by Status</h3></div>
              <div class="card-body p-0">
                <table class="table m-0 align-middle">
                  <tbody>
                    @foreach($statusBreakdown as $row)
                      <tr>
                        <td><span class="badge {{ $row['status']->badgeClass() }}">{{ $row['status']->label() }}</span></td>
                        <td class="text-right font-weight-bold">{{ $row['count'] }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- ══════════ RECENT ACTIVITY ══════════ --}}
          <div class="col-md-7">
            <div class="card">
              <div class="card-header"><h3 class="card-title">Recent Activity</h3></div>
              <div class="card-body p-0">
                @if($recentActivity->isEmpty())
                  <p class="text-muted text-center py-5 mb-0">No activity logged yet.</p>
                @else
                  <div class="review-feed">
                    @foreach($recentActivity as $activity)
                      <div class="review-feed-item">
                        <span class="crm-avatar-badge">{{ strtoupper(substr($activity->admin->name ?? 'S', 0, 1)) }}</span>
                        <div class="review-feed-body">
                          <div class="d-flex justify-content-between align-items-start">
                            <strong>{{ $activity->admin->name ?? 'System' }}</strong>
                            <span class="text-muted small">{{ $activity->created_at->diffForHumans() }}</span>
                          </div>
                          <div class="text-muted small mb-1">
                            {{ $activity->leadable->name ?? 'Deleted lead' }}
                            @if($activity->leadable)
                              &middot; <a href="{{ route('crm.package-enquiries.show', $activity->leadable_id) }}">View</a>
                            @endif
                          </div>
                          <p class="mb-0 small">{{ \Illuminate\Support\Str::limit($activity->description, 120) }}</p>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
              <div class="card-footer text-center">
                <a href="{{ route('crm.follow-ups.due') }}">View Follow-ups Due</a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>
@endsection
