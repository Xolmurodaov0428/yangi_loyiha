@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
      <p class="text-muted mb-0">Xush kelibsiz, {{ auth()->user()->name ?? 'Admin' }}</p>
    </div>
    <div class="text-muted small">
      <i class="fa fa-calendar me-1"></i><span id="currentTime">{{ now()->format('d.m.Y H:i:s') }}</span>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                <i class="fa fa-users-gear text-white fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <div class="text-muted small">Foydalanuvchilar</div>
              <h3 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h3>
              <small class="text-success"><i class="fa fa-user-tie me-1"></i>{{ \App\Models\User::where('role', 'supervisor')->count() }} rahbar</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);">
                <i class="fa fa-building text-white fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <div class="text-muted small">Tashkilotlar</div>
              <h3 class="mb-0 fw-bold" id="orgCount">{{ \App\Models\Organization::count() }}</h3>
              <small class="text-success"><i class="fa fa-check-circle me-1"></i>{{ \App\Models\Organization::where('is_active', true)->count() }} faol</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);">
                <i class="fa fa-users text-white fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <div class="text-muted small">Talabalar</div>
              <h3 class="mb-0 fw-bold" id="studentCount">{{ \App\Models\Student::count() }}</h3>
              <small class="text-success"><i class="fa fa-user-check me-1"></i>{{ \App\Models\Student::where('is_active', true)->count() }} faol</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#fa709a 0%,#fee140 100%);">
                <i class="fa fa-file-lines text-white fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <div class="text-muted small">Bugungi Davomat</div>
              @php
                $todayAttendance = \App\Models\Attendance::whereDate('date', today())->where('status', 'present')->count();
                $activeStudents = \App\Models\Student::where('is_active', true)->count();
                $attendancePercent = $activeStudents > 0 ? round(($todayAttendance / $activeStudents) * 100) : 0;
              @endphp
              <h3 class="mb-0 fw-bold" id="attendancePercent">{{ $attendancePercent }}%</h3>
              <small class="text-muted">{{ $todayAttendance }} / {{ $activeStudents }} talaba</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 p-2" style="background:rgba(79,70,229,0.1);">
              <i class="fa fa-users-gear fs-4" style="color:#4f46e5;"></i>
            </div>
            <h5 class="mb-0 ms-3 fw-bold">Foydalanuvchilar</h5>
          </div>
          <p class="text-muted small mb-3">Barcha foydalanuvchilarni boshqarish (admin, rahbar, talaba)</p>
          <div class="d-grid gap-2">
            <a class="btn btn-primary" href="{{ route('admin.users.index') }}">
              <i class="fa fa-list me-2"></i>Ro'yxatni ko'rish
            </a>
            <a class="btn btn-outline-primary" href="{{ route('admin.users.create') }}">
              <i class="fa fa-plus me-2"></i>Yangi qo'shish
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 p-2" style="background:rgba(245,87,108,0.1);">
              <i class="fa fa-building fs-4" style="color:#f5576c;"></i>
            </div>
            <h5 class="mb-0 ms-3 fw-bold">Tashkilotlar</h5>
          </div>
          <p class="text-muted small mb-3">Amaliyot o'tkaziladigan tashkilotlarni boshqarish</p>
          <div class="d-grid gap-2">
            <button class="btn btn-outline-secondary" disabled>
              <i class="fa fa-clock me-2"></i>Tez orada
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 p-2" style="background:rgba(79,172,254,0.1);">
              <i class="fa fa-users fs-4" style="color:#4facfe;"></i>
            </div>
            <h5 class="mb-0 ms-3 fw-bold">Talabalar & Amaliyot</h5>
          </div>
          <p class="text-muted small mb-3">Talabalarni import qilish va amaliyotga biriktirish</p>
          <div class="d-grid gap-2">
            <a class="btn btn-primary" href="{{ route('admin.students.index') }}">
              <i class="fa fa-list me-2"></i>Ro'yxatni ko'rish
            </a>
            <a class="btn btn-outline-primary" href="{{ route('admin.students.import') }}">
              <i class="fa fa-file-import me-2"></i>Guruh qo'shish
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="row g-3 mt-2">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold mb-0">
              <i class="fa fa-history me-2 text-primary"></i>So'nggi faoliyat
            </h5>
            <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-outline-primary">
              <i class="fa fa-arrow-right me-1"></i>Barchasi
            </a>
          </div>
          @php
            $recentLogs = \App\Models\ActivityLog::with('user')
              ->orderBy('created_at', 'desc')
              ->limit(5)
              ->get();
          @endphp
          @if($recentLogs->count() > 0)
            <div class="list-group list-group-flush">
              @foreach($recentLogs as $log)
                <div class="list-group-item px-0 py-3">
                  <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                      @if($log->action === 'login')
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                          <i class="fa fa-sign-in-alt text-success"></i>
                        </div>
                      @elseif($log->action === 'create')
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                          <i class="fa fa-plus text-primary"></i>
                        </div>
                      @elseif($log->action === 'update')
                        <div class="rounded-circle bg-info bg-opacity-10 p-2">
                          <i class="fa fa-edit text-info"></i>
                        </div>
                      @else
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                          <i class="fa fa-circle text-secondary"></i>
                        </div>
                      @endif
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex justify-content-between">
                        <div>
                          <strong>{{ $log->user->name ?? 'Tizim' }}</strong>
                          <p class="mb-0 text-muted small">{{ $log->description }}</p>
                        </div>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-5 text-muted">
              <i class="fa fa-inbox fs-1 mb-3 opacity-25"></i>
              <p class="mb-0">Hozircha faoliyat yo'q</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">
            <i class="fa fa-chart-simple me-2 text-success"></i>Tezkor statistika
          </h5>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted small">Faol rahbarlar</span>
              <strong>{{ \App\Models\User::where('role', 'supervisor')->where('is_active', true)->count() }}</strong>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-primary" style="width: 100%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted small">Faol talabalar</span>
              <strong>{{ \App\Models\Student::where('is_active', true)->count() }}</strong>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: 100%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted small">Bugungi davomat</span>
              <strong>{{ $attendancePercent }}%</strong>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-warning" style="width: {{ $attendancePercent }}%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">
            <i class="fa fa-link me-2 text-info"></i>Tezkor havolalar
          </h5>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.students.attendance') }}" class="btn btn-sm btn-outline-primary">
              <i class="fa fa-clipboard-check me-2"></i>Davomat
            </a>
            <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-outline-success">
              <i class="fa fa-chart-line me-2"></i>Hisobotlar
            </a>
            <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-outline-info">
              <i class="fa fa-history me-2"></i>Faoliyat jurnali
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Real-time clock
function updateTime() {
  const now = new Date();
  const day = String(now.getDate()).padStart(2, '0');
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const year = now.getFullYear();
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');
  
  document.getElementById('currentTime').textContent = `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
}

// Update every second
setInterval(updateTime, 1000);
updateTime();
</script>
@endsection
