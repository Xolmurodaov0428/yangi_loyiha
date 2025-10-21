@extends('layouts.admin')

@section('title', 'Rahbar ma\'lumotlari')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Rahbar ma'lumotlari</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-sm-4 text-muted">Ism</div>
          <div class="col-sm-8">{{ $supervisor->name }}</div>
        </div>
        <div class="row mb-2">
          <div class="col-sm-4 text-muted">Login</div>
          <div class="col-sm-8">{{ $supervisor->username ?? '—' }}</div>
        </div>
        <div class="row mb-2">
          <div class="col-sm-4 text-muted">Email</div>
          <div class="col-sm-8">{{ $supervisor->email }}</div>
        </div>
        <div class="row mb-2">
          <div class="col-sm-4 text-muted">Tasdiq</div>
          <div class="col-sm-8">{!! $supervisor->approved_at ? '<span class="badge bg-success">Tasdiqlangan</span>' : '<span class="badge bg-secondary">Kutilmoqda</span>' !!}</div>
        </div>
        <div class="row mb-2">
          <div class="col-sm-4 text-muted">Status</div>
          <div class="col-sm-8">{!! $supervisor->is_active ? '<span class="badge bg-success">Faol</span>' : '<span class="badge bg-danger">O\'chiq</span>' !!}</div>
        </div>
        <div class="mt-3 d-flex flex-wrap gap-2">
          <a href="{{ route('admin.supervisors.edit', $supervisor) }}" class="btn btn-warning">Tahrirlash</a>
          <form method="POST" action="{{ route('admin.supervisors.reset-password', $supervisor) }}" onsubmit="return confirm('Yangi vaqtinchalik parol yaratiladi. Davom etasizmi?')">
            @csrf
            <button class="btn btn-outline-primary">Parolni tiklash</button>
          </form>
          @if(!$supervisor->approved_at)
            <form method="POST" action="{{ route('admin.supervisors.approve', $supervisor) }}">
              @csrf
              <button class="btn btn-success">Tasdiqlash</button>
            </form>
          @endif
          @if($supervisor->is_active)
            <form method="POST" action="{{ route('admin.supervisors.deactivate', $supervisor) }}">
              @csrf
              <button class="btn btn-outline-secondary">Faolligini o'chirish</button>
            </form>
          @endif
          <a href="{{ route('admin.supervisors.index') }}" class="btn btn-link">← Ro'yxat</a>
        </div>
      </div>
    </div>
</div>
@endsection
