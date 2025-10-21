@extends('layouts.admin')

@section('title', 'Foydalanuvchilar')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">Foydalanuvchilar</h1>
      <p class="text-muted mb-0">Barcha foydalanuvchilarni boshqarish</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
      <i class="fa fa-user-plus me-2"></i>Yangi qo'shish
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control" placeholder="Qidirish (ism, email, login)..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="role" class="form-select">
            <option value="">Barcha rollar</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Rahbar</option>
            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Talaba</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select">
            <option value="">Barcha holatlar</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Bloklangan</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fa fa-filter me-1"></i>Filtr
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Users Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Foydalanuvchi</th>
              <th>Login</th>
              <th>Rol</th>
              <th>Holat</th>
              <th>Tasdiqlangan</th>
              <th>Davomat huquqi</th>
              <th>Qo'shilgan</th>
              <th>Amallar</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              <tr>
                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                      <i class="fa fa-user text-primary"></i>
                    </div>
                    <div>
                      <div class="fw-semibold">{{ $user->name }}</div>
                      <small class="text-muted">{{ $user->email }}</small>
                    </div>
                  </div>
                </td>
                <td>{{ $user->username ?? '—' }}</td>
                <td>
                  @if($user->role === 'admin')
                    <span class="badge bg-danger">
                      <i class="fa fa-shield-halved me-1"></i>Admin
                    </span>
                  @elseif($user->role === 'supervisor')
                    <span class="badge bg-primary">
                      <i class="fa fa-user-tie me-1"></i>Rahbar
                    </span>
                  @elseif($user->role === 'student')
                    <span class="badge bg-info">
                      <i class="fa fa-graduation-cap me-1"></i>Talaba
                    </span>
                  @else
                    <span class="badge bg-secondary">{{ $user->role }}</span>
                  @endif
                </td>
                <td>
                  @if($user->is_active)
                    <span class="badge bg-success">
                      <i class="fa fa-check-circle me-1"></i>Faol
                    </span>
                  @else
                    <span class="badge bg-danger">
                      <i class="fa fa-ban me-1"></i>Bloklangan
                    </span>
                  @endif
                </td>
                <td>
                  @if($user->approved)
                    <span class="badge bg-success">
                      <i class="fa fa-check me-1"></i>Ha
                    </span>
                  @else
                    <span class="badge bg-warning">
                      <i class="fa fa-clock me-1"></i>Yo'q
                    </span>
                  @endif
                </td>
                <td>
                  @if($user->role === 'supervisor')
                    <form method="POST" action="{{ route('admin.supervisors.toggle-attendance', $user) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm {{ $user->can_mark_attendance ? 'btn-success' : 'btn-danger' }}">
                        <i class="fa {{ $user->can_mark_attendance ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                        {{ $user->can_mark_attendance ? 'Yoqilgan' : 'O\'chirilgan' }}
                      </button>
                    </form>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <small class="text-muted">{{ $user->created_at->format('d.m.Y') }}</small>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light border" title="Ko'rish">
                      <i class="fa fa-eye text-primary"></i>
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-light border" title="Tahrirlash">
                      <i class="fa fa-edit text-warning"></i>
                    </a>
                    @if($user->role !== 'admin' && $user->id !== auth()->id())
                      <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light border" title="{{ $user->is_active ? 'Bloklash' : 'Faollashtirish' }}">
                          <i class="fa fa-{{ $user->is_active ? 'ban text-danger' : 'check text-success' }}"></i>
                        </button>
                      </form>
                    @endif
                    @if($user->id !== auth()->id() && $user->role !== 'admin')
                      <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light border" title="O'chirish">
                          <i class="fa fa-trash text-danger"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5">
                  <i class="fa fa-users fs-1 text-muted opacity-25 mb-3 d-block"></i>
                  <p class="text-muted">Foydalanuvchilar topilmadi</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $users->links() }}
    </div>
  </div>

  <!-- Statistics -->
  <div class="row g-3 mt-3">
    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
              <i class="fa fa-users text-primary fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Jami</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h4>
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
              <i class="fa fa-shield-halved text-danger fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Adminlar</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\User::where('role', 'admin')->count() }}</h4>
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
              <i class="fa fa-user-tie text-info fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Rahbarlar</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\User::where('role', 'supervisor')->count() }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success bg-opacity-10 p-3">
              <i class="fa fa-graduation-cap text-success fs-5"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Talabalar</div>
              <h4 class="mb-0 fw-bold">{{ \App\Models\User::where('role', 'student')->count() }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
