@extends('layouts.supervisor')

@section('title', 'Profil')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-user-circle me-2 text-success"></i>Profil
            </h1>
            <p class="text-muted mb-0">Shaxsiy ma'lumotlarni ko'rish va tahrirlash</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width:120px;height:120px;background:linear-gradient(135deg,#059669 0%,#047857 100%);">
                            <i class="fa fa-user-tie text-white" style="font-size:3rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fa fa-shield-halved me-1"></i>{{ ucfirst($user->role) }}
                    </p>
                    <div class="d-grid gap-2">
                        <div class="text-start p-2 bg-light rounded">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $user->email }}</strong>
                        </div>
                        @if($user->username)
                            <div class="text-start p-2 bg-light rounded">
                                <small class="text-muted d-block">Username</small>
                                <strong>{{ $user->username }}</strong>
                            </div>
                        @endif
                        <div class="text-start p-2 bg-light rounded">
                            <small class="text-muted d-block">Ro'yxatdan o'tgan</small>
                            <strong>{{ $user->created_at->format('d.m.Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="fa fa-chart-line me-2 text-primary"></i>Statistika
                    </h5>
                    <div class="d-grid gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fa fa-users me-2"></i>Talabalar
                            </span>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $stats['total_students'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fa fa-layer-group me-2"></i>Guruhlar
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">{{ $stats['total_groups'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fa fa-clipboard-check me-2"></i>Jami davomat
                            </span>
                            <span class="badge bg-info bg-opacity-10 text-info fw-bold">{{ $stats['total_attendances'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fa fa-calendar me-2"></i>Bu oy
                            </span>
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-bold">{{ $stats['attendance_this_month'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Forms -->
        <div class="col-lg-8">
            <!-- Update Profile Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">
                        <i class="fa fa-edit me-2 text-primary"></i>Profil ma'lumotlarini tahrirlash
                    </h5>
                    <form action="{{ route('supervisor.profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ism Familiya <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="{{ old('username', $user->username) }}">
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Saqlash
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">
                        <i class="fa fa-lock me-2 text-warning"></i>Parolni o'zgartirish
                    </h5>
                    <form action="{{ route('supervisor.profile.password') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">Joriy parol <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Yangi parol <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                                <small class="text-muted">Kamida 8 ta belgi</small>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Parolni tasdiqlash <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-key me-2"></i>Parolni o'zgartirish
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Logs Link -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title fw-bold mb-1">
                                <i class="fa fa-history me-2 text-info"></i>Faoliyat tarixi
                            </h5>
                            <p class="text-muted mb-0 small">Tizimda amalga oshirilgan harakatlar tarixi</p>
                        </div>
                        <a href="{{ route('supervisor.profile.activity-logs') }}" class="btn btn-outline-info">
                            <i class="fa fa-eye me-1"></i>Ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
