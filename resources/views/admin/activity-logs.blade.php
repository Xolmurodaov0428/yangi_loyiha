@extends('layouts.admin')

@section('title', 'Faoliyat Jurnali')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="mb-4">
    <h1 class="h3 mb-1 fw-bold">Faoliyat Jurnali</h1>
    <p class="text-muted mb-0">Tizimdagi barcha harakatlar tarixi</p>
  </div>

  <!-- Activity Timeline -->
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width: 50px;">#</th>
              <th style="width: 150px;">Vaqt</th>
              <th style="width: 150px;">Foydalanuvchi</th>
              <th style="width: 120px;">Harakat</th>
              <th>Tavsif</th>
              <th style="width: 120px;">IP Manzil</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              <tr>
                <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                <td>
                  <small class="text-muted">
                    <i class="fa fa-clock me-1"></i>
                    {{ $log->created_at->format('d.m.Y H:i') }}
                  </small>
                </td>
                <td>
                  @if($log->user)
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-primary bg-opacity-10 p-1 me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-user text-primary" style="font-size: 0.75rem;"></i>
                      </div>
                      <div>
                        <div class="fw-semibold" style="font-size: 0.875rem;">{{ $log->user->name }}</div>
                        <small class="text-muted">{{ $log->user->role }}</small>
                      </div>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @if($log->action === 'login')
                    <span class="badge bg-success">
                      <i class="fa fa-sign-in-alt me-1"></i>Kirish
                    </span>
                  @elseif($log->action === 'create')
                    <span class="badge bg-primary">
                      <i class="fa fa-plus me-1"></i>Yaratish
                    </span>
                  @elseif($log->action === 'update')
                    <span class="badge bg-info">
                      <i class="fa fa-edit me-1"></i>Tahrirlash
                    </span>
                  @elseif($log->action === 'delete')
                    <span class="badge bg-danger">
                      <i class="fa fa-trash me-1"></i>O'chirish
                    </span>
                  @elseif($log->action === 'activate')
                    <span class="badge bg-success">
                      <i class="fa fa-check me-1"></i>Faollashtirish
                    </span>
                  @elseif($log->action === 'deactivate')
                    <span class="badge bg-warning">
                      <i class="fa fa-ban me-1"></i>Bloklash
                    </span>
                  @else
                    <span class="badge bg-secondary">{{ $log->action }}</span>
                  @endif
                </td>
                <td>
                  <div>{{ $log->description }}</div>
                  @if($log->model_type)
                    <small class="text-muted">
                      <i class="fa fa-tag me-1"></i>{{ $log->model_type }}
                      @if($log->model_id)
                        #{{ $log->model_id }}
                      @endif
                    </small>
                  @endif
                </td>
                <td>
                  <code style="font-size: 0.75rem;">{{ $log->ip_address ?? '—' }}</code>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5">
                  <i class="fa fa-history fs-1 text-muted opacity-25 mb-3 d-block"></i>
                  <p class="text-muted mb-0">Hozircha faoliyat yo'q</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $logs->links() }}
    </div>
  </div>

  <!-- Statistics -->
  <div class="row g-3 mt-3">
    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success bg-opacity-10 p-3">
              <i class="fa fa-sign-in-alt text-success fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Kirishlar</div>
              <h4 class="mb-0 fw-bold">
                @php
                  try {
                    echo \App\Models\ActivityLog::where('action', 'login')->count();
                  } catch (\Exception $e) {
                    echo '0';
                  }
                @endphp
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
              <i class="fa fa-plus text-primary fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Yaratilgan</div>
              <h4 class="mb-0 fw-bold">
                @php
                  try {
                    echo \App\Models\ActivityLog::where('action', 'create')->count();
                  } catch (\Exception $e) {
                    echo '0';
                  }
                @endphp
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-info bg-opacity-10 p-3">
              <i class="fa fa-edit text-info fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Tahrirlangan</div>
              <h4 class="mb-0 fw-bold">
                @php
                  try {
                    echo \App\Models\ActivityLog::where('action', 'update')->count();
                  } catch (\Exception $e) {
                    echo '0';
                  }
                @endphp
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger bg-opacity-10 p-3">
              <i class="fa fa-trash text-danger fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">O'chirilgan</div>
              <h4 class="mb-0 fw-bold">
                @php
                  try {
                    echo \App\Models\ActivityLog::where('action', 'delete')->count();
                  } catch (\Exception $e) {
                    echo '0';
                  }
                @endphp
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity by User -->
  <div class="row g-3 mt-3">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-users text-primary me-2"></i>Foydalanuvchilar bo'yicha faoliyat
          </h5>
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th>Foydalanuvchi</th>
                  <th>Jami harakatlar</th>
                  <th>Oxirgi faoliyat</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $userActivities = collect([]);
                  try {
                    $userActivities = \App\Models\ActivityLog::with('user')
                      ->selectRaw('user_id, COUNT(*) as total, MAX(created_at) as last_activity')
                      ->groupBy('user_id')
                      ->orderBy('total', 'desc')
                      ->limit(10)
                      ->get();
                  } catch (\Exception $e) {
                    // user_id ustuni mavjud emas
                  }
                @endphp
                @forelse($userActivities as $activity)
                  @if($activity->user)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                            <i class="fa fa-user text-primary"></i>
                          </div>
                          <div>
                            <div class="fw-semibold">{{ $activity->user->name }}</div>
                            <small class="text-muted">{{ $activity->user->email }}</small>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge bg-primary">{{ $activity->total }}</span></td>
                      <td><small class="text-muted">{{ \Carbon\Carbon::parse($activity->last_activity)->diffForHumans() }}</small></td>
                    </tr>
                  @endif
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                      <i class="fa fa-inbox fs-3 mb-2 opacity-25 d-block"></i>
                      Ma'lumotlar yo'q
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
