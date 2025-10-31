@extends('layouts.admin')

@section('title', 'Talabalar ro\'yxati')

@section('content')
<style>
.hover-shadow {
  transition: all 0.3s ease;
}
.hover-shadow:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">Talabalar ro'yxati</h1>
      <p class="text-muted mb-0">
        @if($selectedGroup)
          {{ $selectedGroup->name }} - {{ $selectedGroup->faculty }}
        @else
          Guruhlarni tanlang
        @endif
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.students.import') }}" class="btn btn-success">
        <i class="fa fa-file-import me-2"></i>Guruh qo'shish
      </a>
      <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <i class="fa fa-user-plus me-2"></i>Bitta qo'shish
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('import_errors'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-triangle me-2"></i>
      <strong>Import xatoliklari:</strong>
      <ul class="mb-0 mt-2">
        @foreach(session('import_errors') as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Groups Grid -->
  @if(!$selectedGroup)
    <div class="row g-3 mb-4">
      @forelse($groups as $group)
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100 hover-shadow">
            <div class="card-body p-4">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center flex-grow-1">
                  <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="fa fa-users text-primary fs-3"></i>
                  </div>
                  <div>
                    <h5 class="mb-1 fw-bold">{{ $group->name }}</h5>
                    <p class="text-muted mb-0 small">{{ $group->faculty }}</p>
                  </div>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editGroupModal{{ $group->id }}">
                        <i class="fa fa-edit me-2"></i>Tahrirlash
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('admin.students.index', ['group' => $group->name]) }}">
                        <i class="fa fa-eye me-2"></i>Talabalarni ko'rish
                      </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                          <i class="fa fa-trash me-2"></i>O'chirish
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
              
              <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-primary">
                  <i class="fa fa-user me-1"></i>{{ $group->students_count }} talaba
                </span>
                @if($group->supervisors && $group->supervisors->count() > 0)
                  <span class="badge bg-success">
                    <i class="fa fa-user-tie me-1"></i>{{ $group->supervisors->count() }} rahbar
                  </span>
                @else
                  <span class="badge bg-secondary">
                    <i class="fa fa-user-slash me-1"></i>Rahbar yo'q
                  </span>
                @endif
              </div>

              <a href="{{ route('admin.students.index', ['group' => $group->name]) }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="fa fa-arrow-right me-2"></i>Talabalarni ko'rish
              </a>
            </div>
          </div>

          <!-- Edit Group Modal -->
          <div class="modal fade" id="editGroupModal{{ $group->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Guruhni tahrirlash</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.groups.update', $group->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Guruh nomi</label>
                      <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Fakultet</label>
                      <input type="text" name="faculty" class="form-control" value="{{ $group->faculty }}" required>
                    </div>
                    <div class="mb-3">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active{{ $group->id }}" value="1" {{ $group->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active{{ $group->id }}">
                          Faol
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
              <i class="fa fa-inbox fs-1 text-muted opacity-25 mb-3"></i>
              <h5 class="text-muted">Guruhlar topilmadi</h5>
              <p class="text-muted mb-3">Guruh qo'shish uchun import funksiyasidan foydalaning</p>
              <a href="{{ route('admin.students.import') }}" class="btn btn-primary">
                <i class="fa fa-file-import me-2"></i>Guruh qo'shish
              </a>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  @else
    <!-- Back Button -->
    <div class="mb-3">
      <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-2"></i>Guruhlar ro'yxatiga qaytish
      </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3">
          <input type="hidden" name="group" value="{{ request('group') }}">
          <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Qidirish..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3">
            <select name="organization" class="form-select">
              <option value="">Barcha tashkilotlar</option>
              @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select name="status" class="form-select">
              <option value="">Barcha</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nofaol</option>
            </select>
          </div>
          <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">
              <i class="fa fa-filter"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Students Table -->
    @if($students->count() > 0)
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>F.I.Sh.</th>
                <th>Guruh / Fakultet</th>
                <th>Login</th>
                <th>Tashkilot</th>
                <th>Amaliyot muddati</th>
                <th>Holat</th>
                <th>Amallar</th>
              </tr>
            </thead>
            <tbody>
              @foreach($students as $student)
                <tr>
                  <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2">
                        <i class="fa fa-graduation-cap text-info"></i>
                      </div>
                      <div class="fw-semibold">{{ $student->full_name }}</div>
                    </div>
                  </td>
                  <td>
                    <div>{{ $student->group_name ?? '—' }}</div>
                    @if($student->faculty)
                      <small class="text-muted">{{ $student->faculty }}</small>
                    @endif
                  </td>
                  <td><code>{{ $student->username }}</code></td>
                  <td>{{ $student->organization->name ?? '—' }}</td>
                  <td>
                    @if($student->internship_start_date && $student->internship_end_date)
                      <small>{{ $student->internship_start_date->format('d.m.Y') }} - {{ $student->internship_end_date->format('d.m.Y') }}</small>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    @if($student->is_active)
                      <span class="badge bg-success"><i class="fa fa-check me-1"></i>Faol</span>
                    @else
                      <span class="badge bg-danger"><i class="fa fa-ban me-1"></i>Nofaol</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-primary" title="Ko'rish">
                        <i class="fa fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-warning" title="Tahrirlash">
                        <i class="fa fa-edit"></i>
                      </a>
                      <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="O'chirish">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {{ $students->links() }}
      </div>
    </div>
    @else
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <i class="fa fa-users fs-1 text-muted opacity-25 mb-3 d-block"></i>
          <h5 class="text-muted">Talabalar topilmadi</h5>
          <p class="text-muted mb-3">Bu guruhda hali talabalar yo'q</p>
        </div>
      </div>
    @endif
  @endif

  <!-- Statistics -->
  <div class="row g-3 mt-3">
    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success bg-opacity-10 p-3">
              <i class="fa fa-users text-success fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Jami talabalar</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\Student::count() }}</h4>
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
              <i class="fa fa-layer-group text-info fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Guruhlar</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\Group::count() }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
              <i class="fa fa-building text-warning fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Tashkilotlar</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\Organization::count() }}</h4>
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
              <i class="fa fa-user-check text-primary fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Faol</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\Student::where('is_active', true)->count() }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
