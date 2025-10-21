@extends('layouts.supervisor')

@section('title', 'Rahbar Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Rahbar Dashboard</h1>
            <p class="text-muted mb-0">Xush kelibsiz, {{ auth()->user()->name ?? 'Rahbar' }}</p>
        </div>
        <div class="text-muted small">
            <i class="fa fa-calendar me-1"></i><span id="currentTime">{{ now()->format('d.m.Y H:i:s') }}</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#059669 0%,#047857 100%);">
                                <i class="fa fa-users text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Biriktirilgan Talabalar</div>
                            <h3 class="mb-0 fw-bold">@php try { echo \App\Models\Student::where('supervisor_id', auth()->id())->count(); } catch (\Exception $e) { echo 0; } @endphp</h3>
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
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);">
                                <i class="fa fa-clipboard-check text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Bugungi Davomat</div>
                            <h3 class="mb-0 fw-bold">@php try { echo \App\Models\Attendance::whereDate('date', today())->whereHas('student', function($q) { $q->where('supervisor_id', auth()->id()); })->where('status', 'present')->count(); } catch (\Exception $e) { echo 0; } @endphp</h3>
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
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);">
                                <i class="fa fa-book text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Kutilayotgan Kundaliklar</div>
                            <h3 class="mb-0 fw-bold">0</h3> <!-- Placeholder -->
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
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);">
                                <i class="fa fa-star text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Baholangan</div>
                            <h3 class="mb-0 fw-bold">0</h3> <!-- Placeholder -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Groups -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0">
                    <i class="fa fa-layer-group me-2 text-success"></i>Biriktirilgan Guruhlar
                </h5>
                <div class="text-muted small">
                    @php $groupCount = isset($groups) ? $groups->count() : 0; @endphp
                    Jami: {{ $groupCount }} ta
                </div>
            </div>

            @if(isset($groups) && $groups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Guruhlar</th>
                                <th>Fakultet</th>
                                <th>Faol talabalar</th>
                                <th>Holati</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $index => $group)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $group->name }}</strong>
                                        <!-- <div class="text-muted small">ID: {{ $group->id }}</div> -->
                                    </td>
                                    <td>{{ $group->faculty ?? 'Belgilanmagan' }}</td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fa fa-users me-1"></i>{{ $group->students_count ?? 0 }} ta
                                        </span>
                                    </td>
                                    <td>
                                        @if($group->is_active ?? true)
                                            <span class="badge bg-success">Faol</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-layer-group fs-1 mb-3 opacity-25"></i>
                    <p class="mb-0">Hozircha sizga biriktirilgan guruhlar mavjud emas</p>
                    <small class="text-muted">Administrator guruh biriktirganda bu yerda ko'rinadi</small>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 p-2" style="background:rgba(5,150,105,0.1);">
                            <i class="fa fa-users fs-4" style="color:#059669;"></i>
                        </div>
                        <h5 class="mb-0 ms-3 fw-bold">Talabalar</h5>
                    </div>
                    <p class="text-muted small mb-3">Biriktirilgan talabalarni boshqarish va kuzatish</p>
                    <div class="d-grid gap-2">
                        <a class="btn btn-success" href="{{ route('supervisor.students') }}">
                            <i class="fa fa-list me-2"></i>Ro'yxatni ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 p-2" style="background:rgba(59,130,246,0.1);">
                            <i class="fa fa-clipboard-check fs-4" style="color:#3b82f6;"></i>
                        </div>
                        <h5 class="mb-0 ms-3 fw-bold">Davomat</h5>
                    </div>
                    <p class="text-muted small mb-3">Talabalarning davomatini tekshirish va yangilash</p>
                    <div class="d-grid gap-2">
                        <a class="btn btn-primary" href="{{ route('supervisor.attendance') }}">
                            <i class="fa fa-calendar-check me-2"></i>Davomat sahifasi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 p-2" style="background:rgba(245,158,11,0.1);">
                            <i class="fa fa-book fs-4" style="color:#f59e0b;"></i>
                        </div>
                        <h5 class="mb-0 ms-3 fw-bold">Kundaliklar</h5>
                    </div>
                    <p class="text-muted small mb-3">Talabalarning kundalik yozuvlarini tekshirish</p>
                    <div class="d-grid gap-2">
                        <a class="btn btn-warning" href="{{ route('supervisor.logbooks') }}">
                            <i class="fa fa-eye me-2"></i>Kundaliklarni ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
setInterval(updateTime, 1000);
updateTime();
</script>
@endsection
