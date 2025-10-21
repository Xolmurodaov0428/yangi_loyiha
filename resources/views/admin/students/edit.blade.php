@extends('layouts.admin')

@section('title', 'Talabani tahrirlash')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold">Talabani tahrirlash</h1>
        <p class="text-muted mb-0">{{ $student->full_name }}</p>
      </div>

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

      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('admin.students.update', $student) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <!-- Full Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-user text-primary me-1"></i>F.I.Sh. <span class="text-danger">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" class="form-control" required>
              </div>

              <!-- Username -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-at text-primary me-1"></i>Login <span class="text-danger">*</span>
                </label>
                <input type="text" name="username" value="{{ old('username', $student->username) }}" class="form-control" required>
              </div>

              <!-- Password -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-lock text-primary me-1"></i>Yangi parol (ixtiyoriy)
                </label>
                <input type="password" name="password" class="form-control">
                <small class="text-muted">Bo'sh qoldiring, agar o'zgartirmoqchi bo'lmasangiz</small>
              </div>

              <!-- Group Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-layer-group text-primary me-1"></i>Guruh nomi
                </label>
                <input type="text" name="group_name" value="{{ old('group_name', $student->group_name) }}" class="form-control">
              </div>

              <!-- Faculty -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-building-columns text-primary me-1"></i>Fakultet
                </label>
                <input type="text" name="faculty" value="{{ old('faculty', $student->faculty) }}" class="form-control">
              </div>

              <!-- Organization -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-building text-primary me-1"></i>Biriktirilgan tashkilot
                </label>
                <select name="organization_id" class="form-select">
                  <option value="">Tanlang...</option>
                  @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id', $student->organization_id) == $org->id ? 'selected' : '' }}>
                      {{ $org->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Internship Start Date -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-calendar-check text-primary me-1"></i>Amaliyot boshlanishi
                </label>
                <input type="date" name="internship_start_date" value="{{ old('internship_start_date', $student->internship_start_date?->format('Y-m-d')) }}" class="form-control">
              </div>

              <!-- Internship End Date -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-calendar-xmark text-primary me-1"></i>Amaliyot tugashi
                </label>
                <input type="date" name="internship_end_date" value="{{ old('internship_end_date', $student->internship_end_date?->format('Y-m-d')) }}" class="form-control">
              </div>

              <!-- Active Status -->
              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold" for="is_active">
                    <i class="fa fa-check-circle text-success me-1"></i>Faol
                  </label>
                </div>
              </div>

              <!-- Buttons -->
              <div class="col-12">
                <hr class="my-3">
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-2"></i>Saqlash
                  </button>
                  <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fa fa-times me-2"></i>Bekor qilish
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
