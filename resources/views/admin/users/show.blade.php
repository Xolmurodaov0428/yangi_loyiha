@extends('layouts.admin')

@section('title', 'Foydalanuvchi ma\'lumotlari')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 mb-1 fw-bold">Foydalanuvchi ma'lumotlari</h1>
          <p class="text-muted mb-0">{{ $user->name }}</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
            <i class="fa fa-edit me-2"></i>Tahrirlash
          </a>
          <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-2"></i>Orqaga
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <!-- User Info Card -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-12">
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-4 me-3">
                  <i class="fa fa-user text-primary fs-1"></i>
                </div>
                <div>
                  <h4 class="mb-1">{{ $user->name }}</h4>
                  <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
              </div>
            </div>

            <div class="col-12"><hr></div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Login</label>
              <div class="fw-semibold">{{ $user->username ?? '—' }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Rol</label>
              <div>
                @if($user->role === 'admin')
                  <span class="badge bg-danger fs-6">
                    <i class="fa fa-shield-halved me-1"></i>Admin
                  </span>
                @elseif($user->role === 'supervisor')
                  <span class="badge bg-primary fs-6">
                    <i class="fa fa-user-tie me-1"></i>Rahbar
                  </span>
                @elseif($user->role === 'student')
                  <span class="badge bg-info fs-6">
                    <i class="fa fa-graduation-cap me-1"></i>Talaba
                  </span>
                @else
                  <span class="badge bg-secondary fs-6">{{ $user->role }}</span>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Holat</label>
              <div>
                @if($user->is_active)
                  <span class="badge bg-success fs-6">
                    <i class="fa fa-check-circle me-1"></i>Faol
                  </span>
                @else
                  <span class="badge bg-danger fs-6">
                    <i class="fa fa-ban me-1"></i>Bloklangan
                  </span>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Tasdiqlangan</label>
              <div>
                @if($user->approved)
                  <span class="badge bg-success fs-6">
                    <i class="fa fa-check me-1"></i>Ha
                  </span>
                @else
                  <span class="badge bg-warning fs-6">
                    <i class="fa fa-clock me-1"></i>Yo'q
                  </span>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Qo'shilgan sana</label>
              <div class="fw-semibold">{{ $user->created_at->format('d.m.Y H:i') }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Oxirgi yangilanish</label>
              <div class="fw-semibold">{{ $user->updated_at->format('d.m.Y H:i') }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Groups Card (for supervisors) -->
      @if($user->role === 'supervisor')
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body p-4">
            <h5 class="card-title mb-3">
              <i class="fa fa-users me-2 text-primary"></i>Biriktirilgan guruhlar
            </h5>
            @if($user->groups->count() > 0)
              <div class="row g-3">
                @foreach($user->groups as $group)
                  <div class="col-md-6">
                    <div class="card border-0 bg-light">
                      <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                            <i class="fa fa-users text-primary"></i>
                          </div>
                          <div>
                            <h6 class="mb-0 fw-bold">{{ $group->name }}</h6>
                            <small class="text-muted">{{ $group->faculty }}</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="mt-3">
                <small class="text-muted">
                  <i class="fa fa-info-circle me-1"></i>Jami: <strong>{{ $user->groups->count() }}</strong> ta guruh
                </small>
              </div>
            @else
              <div class="text-center text-muted py-4">
                <i class="fa fa-inbox fs-1 mb-3 opacity-25"></i>
                <p class="mb-0">Hech qanday guruh biriktirilmagan</p>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary mt-3">
                  <i class="fa fa-plus me-1"></i>Guruh biriktirish
                </a>
              </div>
            @endif
          </div>
        </div>
      @endif

      <!-- Actions Card -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h5 class="card-title mb-3">
            <i class="fa fa-cog me-2 text-primary"></i>Amallar
          </h5>
          <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
              @csrf
              @if($user->is_active)
                <button type="submit" class="btn btn-outline-danger">
                  <i class="fa fa-ban me-2"></i>Bloklash
                </button>
              @else
                <button type="submit" class="btn btn-outline-success">
                  <i class="fa fa-check me-2"></i>Faollashtirish
                </button>
              @endif
            </form>

            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-warning">
              <i class="fa fa-edit me-2"></i>Tahrirlash
            </a>

            @if($user->id !== auth()->id())
              <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                  <i class="fa fa-trash me-2"></i>O'chirish
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
