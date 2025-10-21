@extends('layouts.admin')

@section('title', 'Sozlamalar')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="mb-4">
    <h1 class="h3 mb-1 fw-bold">Sozlamalar</h1>
    <p class="text-muted mb-0">Tizim va profil sozlamalarini boshqarish</p>
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

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>
      <strong>Xatoliklar:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    <!-- Profile Settings -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-user text-primary me-2"></i>Profil sozlamalari
          </h5>
          <form method="POST" action="{{ route('admin.settings.profile') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Ism</label>
              <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
            </div>
            <hr class="my-3">
            <div class="mb-3">
              <label class="form-label fw-semibold">Joriy parol</label>
              <input type="password" name="current_password" class="form-control" placeholder="Parolni o'zgartirish uchun kiriting">
              <small class="text-muted">Parolni o'zgartirmasangiz, bo'sh qoldiring</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Yangi parol</label>
              <input type="password" name="new_password" class="form-control" placeholder="Kamida 6 ta belgi">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Yangi parolni tasdiqlash</label>
              <input type="password" name="new_password_confirmation" class="form-control" placeholder="Parolni qayta kiriting">
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save me-2"></i>Saqlash
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- System Settings -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fa fa-cog text-success me-2"></i>Tizim sozlamalari
          </h5>
          <form method="POST" action="{{ route('admin.settings.system') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Tizim nomi</label>
              <input type="text" name="app_name" class="form-control" value="{{ \App\Models\Setting::get('app_name', 'Amaliyot Tizimi') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Til</label>
              <select name="app_locale" class="form-select" required>
                <option value="uz" {{ \App\Models\Setting::get('app_locale') === 'uz' ? 'selected' : '' }}>🇺🇿 O'zbekcha</option>
                <option value="ru" {{ \App\Models\Setting::get('app_locale') === 'ru' ? 'selected' : '' }}>🇷🇺 Русский</option>
                <option value="en" {{ \App\Models\Setting::get('app_locale') === 'en' ? 'selected' : '' }}>🇬🇧 English</option>
              </select>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" name="dark_mode" class="form-check-input" id="darkMode" {{ \App\Models\Setting::get('dark_mode') === '1' ? 'checked' : '' }}>
              <label class="form-check-label" for="darkMode">
                <i class="fa fa-moon me-1"></i>Qorong'i rejim (Dark Mode)
              </label>
            </div>
            <button type="submit" class="btn btn-success">
              <i class="fa fa-save me-2"></i>Saqlash
            </button>
          </form>
        </div>
      </div>

      <!-- Telegram Settings -->
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-4">
            <i class="fab fa-telegram text-info me-2"></i>Telegram sozlamalari
          </h5>
          <form method="POST" action="{{ route('admin.settings.telegram') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Bot Token</label>
              <input type="text" name="telegram_bot_token" class="form-control" value="{{ \App\Models\Setting::get('telegram_bot_token') }}" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
              <small class="text-muted">@BotFather dan olingan token</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Chat ID</label>
              <input type="text" name="telegram_chat_id" class="form-control" value="{{ \App\Models\Setting::get('telegram_chat_id') }}" placeholder="123456789">
              <small class="text-muted">Backup yuborish uchun chat ID</small>
            </div>
            <button type="submit" class="btn btn-info text-white">
              <i class="fa fa-save me-2"></i>Saqlash
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Backup Section -->
  <div class="row g-3 mt-2">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h5 class="card-title fw-bold mb-2">
                <i class="fa fa-database text-warning me-2"></i>Ma'lumotlar bazasini zaxiralash
              </h5>
              <p class="text-muted mb-0">
                Barcha ma'lumotlarni SQL faylga saqlash va Telegram orqali yuborish
              </p>
              <small class="text-muted">
                <i class="fa fa-info-circle me-1"></i>
                Backup <code>storage/app/backups/</code> papkasiga saqlanadi
              </small>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
              <form method="POST" action="{{ route('admin.settings.backup') }}" onsubmit="return confirm('Backup yaratilsinmi?')">
                @csrf
                <button type="submit" class="btn btn-warning">
                  <i class="fa fa-download me-2"></i>Backup yaratish
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Info Cards -->
  <div class="row g-3 mt-2">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fa fa-server text-primary fs-3"></i>
          </div>
          <h6 class="fw-bold">Server ma'lumotlari</h6>
          <p class="text-muted small mb-0">PHP: {{ phpversion() }}</p>
          <p class="text-muted small mb-0">Laravel: {{ app()->version() }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fa fa-database text-success fs-3"></i>
          </div>
          <h6 class="fw-bold">Ma'lumotlar bazasi</h6>
          <p class="text-muted small mb-0">Jami jadvallar: {{ DB::select('SHOW TABLES') ? count(DB::select('SHOW TABLES')) : 0 }}</p>
          <p class="text-muted small mb-0">Fayl: {{ env('DB_DATABASE') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fa fa-shield-halved text-warning fs-3"></i>
          </div>
          <h6 class="fw-bold">Xavfsizlik</h6>
          <p class="text-muted small mb-0">Oxirgi kirish: {{ auth()->user()->updated_at->diffForHumans() }}</p>
          <p class="text-muted small mb-0">Rol: {{ auth()->user()->role }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
