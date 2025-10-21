@extends('layouts.admin')

@section('title', 'Hisobotlar va Statistika')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="mb-4">
    <h1 class="h3 mb-1 fw-bold">Hisobotlar va Statistika</h1>
    <p class="text-muted mb-0">Umumiy ko'rsatkichlar va diagrammalar</p>
  </div>

  <!-- Main Statistics -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
              <i class="fa fa-graduation-cap text-primary fs-3"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small mb-1">Jami Talabalar</div>
              <h3 class="mb-0 fw-bold">{{ \App\Models\Student::count() }}</h3>
              <small class="text-success">
                <i class="fa fa-arrow-up me-1"></i>
                {{ \App\Models\Student::where('is_active', true)->count() }} faol
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
              <i class="fa fa-building text-warning fs-3"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small mb-1">Tashkilotlar</div>
              <h3 class="mb-0 fw-bold">{{ \App\Models\Organization::count() }}</h3>
              <small class="text-success">
                <i class="fa fa-check-circle me-1"></i>
                {{ \App\Models\Organization::where('is_active', true)->count() }} faol
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-info bg-opacity-10 p-3">
              <i class="fa fa-user-tie text-info fs-3"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small mb-1">Faol Rahbarlar</div>
              <h3 class="mb-0 fw-bold">{{ \App\Models\User::where('role', 'supervisor')->where('is_active', true)->count() }}</h3>
              <small class="text-muted">
                {{ \App\Models\User::where('role', 'supervisor')->count() }} dan
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success bg-opacity-10 p-3">
              <i class="fa fa-calendar-check text-success fs-3"></i>
            </div>
            <div class="ms-3">
              @php
                $today = \App\Models\Attendance::whereDate('date', today())->count();
                $totalStudents = \App\Models\Student::where('is_active', true)->count();
                $attendancePercent = $totalStudents > 0 ? round(($today / $totalStudents) * 100) : 0;
              @endphp
              <div class="text-muted small mb-1">Bugungi Davomat</div>
              <h3 class="mb-0 fw-bold">{{ $attendancePercent }}%</h3>
              <small class="text-muted">{{ $today }} / {{ $totalStudents }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row g-3 mb-4">
    <!-- Active Students Chart -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-chart-pie text-primary me-2"></i>Talabalar Holati
          </h5>
          <div style="height: 300px; position: relative;">
            <canvas id="studentsChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Attendance Chart -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-chart-bar text-success me-2"></i>Haftalik Davomat
          </h5>
          <div style="height: 300px; position: relative;">
            <canvas id="attendanceChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Statistics -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-layer-group text-info me-2"></i>Guruhlar bo'yicha Statistika
          </h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Guruh</th>
                  <th>Talabalar</th>
                  <th>Faol</th>
                  <th>Davomat %</th>
                  <th>Progress</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $groups = \App\Models\Student::select('group_name')
                    ->whereNotNull('group_name')
                    ->groupBy('group_name')
                    ->get();
                @endphp
                @forelse($groups as $group)
                  @php
                    $groupStudents = \App\Models\Student::where('group_name', $group->group_name)->get();
                    $total = $groupStudents->count();
                    $active = $groupStudents->where('is_active', true)->count();
                    $attendanceCount = \App\Models\Attendance::whereIn('student_id', $groupStudents->pluck('id'))
                      ->whereDate('date', today())
                      ->where('status', 'present')
                      ->count();
                    $attendancePercent = $active > 0 ? round(($attendanceCount / $active) * 100) : 0;
                  @endphp
                  <tr>
                    <td><strong>{{ $group->group_name }}</strong></td>
                    <td>{{ $total }}</td>
                    <td><span class="badge bg-success">{{ $active }}</span></td>
                    <td>{{ $attendancePercent }}%</td>
                    <td>
                      <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendancePercent }}%"></div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Ma'lumot yo'q</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-clock text-warning me-2"></i>Amaliyot Muddati
          </h5>
          @php
            // Amaliyot muddati ma'lumotlari - hozircha 0
            $ongoingInternships = 0;
            $completedInternships = 0;
            $upcomingInternships = 0;
            
            // Agar students jadvalida internship ustunlari bo'lsa
            try {
                $ongoingInternships = \App\Models\Student::whereNotNull('internship_start_date')
                  ->whereNotNull('internship_end_date')
                  ->where('internship_start_date', '<=', today())
                  ->where('internship_end_date', '>=', today())
                  ->count();
                $completedInternships = \App\Models\Student::whereNotNull('internship_end_date')
                  ->where('internship_end_date', '<', today())
                  ->count();
                $upcomingInternships = \App\Models\Student::whereNotNull('internship_start_date')
                  ->where('internship_start_date', '>', today())
                  ->count();
            } catch (\Exception $e) {
                // Ustunlar mavjud emas
            }
          @endphp
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Davom etayotgan</span>
              <strong class="text-success">{{ $ongoingInternships }}</strong>
            </div>
            <div class="progress mb-3" style="height: 8px;">
              <div class="progress-bar bg-success" style="width: 100%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Tugagan</span>
              <strong class="text-secondary">{{ $completedInternships }}</strong>
            </div>
            <div class="progress mb-3" style="height: 8px;">
              <div class="progress-bar bg-secondary" style="width: 100%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Boshlanmagan</span>
              <strong class="text-info">{{ $upcomingInternships }}</strong>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-info" style="width: 100%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-trophy text-warning me-2"></i>Top Tashkilotlar
          </h5>
          @php
            $topOrgs = \App\Models\Organization::withCount('students')
              ->orderBy('students_count', 'desc')
              ->take(5)
              ->get();
          @endphp
          <div class="list-group list-group-flush">
            @forelse($topOrgs as $org)
              <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <div>
                  <i class="fa fa-building text-warning me-2"></i>
                  <span>{{ $org->name }}</span>
                </div>
                <span class="badge bg-primary rounded-pill">{{ $org->students_count }}</span>
              </div>
            @empty
              <div class="text-center text-muted py-3">Ma'lumot yo'q</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Students Status Chart
    const studentsCtx = document.getElementById('studentsChart');
    if (studentsCtx) {
      const activeCount = {{ \App\Models\Student::where('is_active', true)->count() }};
      const inactiveCount = {{ \App\Models\Student::where('is_active', false)->count() }};
      
      new Chart(studentsCtx, {
        type: 'doughnut',
        data: {
          labels: ['Faol', 'Nofaol'],
          datasets: [{
            data: [activeCount, inactiveCount],
            backgroundColor: ['#10b981', '#ef4444'],
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 15,
                font: {
                  size: 13
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.parsed || 0;
                  const total = activeCount + inactiveCount;
                  const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                  return label + ': ' + value + ' (' + percentage + '%)';
                }
              }
            }
          }
        }
      });
    }

    // Weekly Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
      @php
        $weekDays = [];
        $presentCounts = [];
        $dayNames = ['Yak', 'Dush', 'Sesh', 'Chor', 'Pay', 'Jum', 'Shan'];
        
        for ($i = 6; $i >= 0; $i--) {
          $date = \Carbon\Carbon::today()->subDays($i);
          $dayOfWeek = $date->dayOfWeek; // 0=Yakshanba, 1=Dushanba, ...
          $weekDays[] = $dayNames[$dayOfWeek] . ' ' . $date->format('d');
          
          $presentCounts[] = \App\Models\Attendance::whereDate('date', $date)
            ->where('status', 'present')
            ->count();
        }
      @endphp
      
      new Chart(attendanceCtx, {
        type: 'bar',
        data: {
          labels: {!! json_encode($weekDays) !!},
          datasets: [{
            label: 'Kelgan talabalar',
            data: {!! json_encode($presentCounts) !!},
            backgroundColor: function(context) {
              const value = context.parsed.y;
              if (value === 0) return '#e5e7eb';
              if (value < 5) return '#fbbf24';
              return '#10b981';
            },
            borderRadius: 8,
            borderSkipped: false,
            barThickness: 40
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: {
                size: 14
              },
              bodyFont: {
                size: 13
              },
              callbacks: {
                title: function(context) {
                  return context[0].label;
                },
                label: function(context) {
                  return 'Kelgan: ' + context.parsed.y + ' talaba';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  size: 12
                },
                callback: function(value) {
                  return Number.isInteger(value) ? value : '';
                }
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 11
                }
              }
            }
          }
        }
      });
    }
  });
</script>
@endsection
