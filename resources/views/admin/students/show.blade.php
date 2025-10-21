@extends('layouts.admin')

@section('title', 'Talaba ma\'lumotlari')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 mb-1 fw-bold">Talaba ma'lumotlari</h1>
          <p class="text-muted mb-0">{{ $student->full_name }}</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning">
            <i class="fa fa-edit me-2"></i>Tahrirlash
          </a>
          <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
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

      <!-- Student Info Card -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-12">
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-4 me-3">
                  <i class="fa fa-graduation-cap text-info fs-1"></i>
                </div>
                <div>
                  <h4 class="mb-1">{{ $student->full_name }}</h4>
                  <p class="text-muted mb-0">
                    <code>{{ $student->username }}</code>
                  </p>
                </div>
              </div>
            </div>

            <div class="col-12"><hr></div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Guruh nomi</label>
              <div class="fw-semibold">{{ $student->group_name ?? '—' }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Fakultet</label>
              <div class="fw-semibold">{{ $student->faculty ?? '—' }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Biriktirilgan tashkilot</label>
              <div>
                @if($student->organization)
                  <span class="badge bg-primary fs-6">
                    <i class="fa fa-building me-1"></i>{{ $student->organization->name }}
                  </span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Holat</label>
              <div>
                @if($student->is_active)
                  <span class="badge bg-success fs-6">
                    <i class="fa fa-check-circle me-1"></i>Faol
                  </span>
                @else
                  <span class="badge bg-danger fs-6">
                    <i class="fa fa-ban me-1"></i>Nofaol
                  </span>
                @endif
              </div>
            </div>

            <div class="col-12"><hr></div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Amaliyot boshlanishi</label>
              <div class="fw-semibold">
                @if($student->internship_start_date)
                  <i class="fa fa-calendar-check text-success me-1"></i>
                  {{ $student->internship_start_date->format('d.m.Y') }}
                @else
                  <span class="text-muted">—</span>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Amaliyot tugashi</label>
              <div class="fw-semibold">
                @if($student->internship_end_date)
                  <i class="fa fa-calendar-xmark text-danger me-1"></i>
                  {{ $student->internship_end_date->format('d.m.Y') }}
                @else
                  <span class="text-muted">—</span>
                @endif
              </div>
            </div>

            @if($student->internship_start_date && $student->internship_end_date)
              <div class="col-12">
                <div class="alert alert-info mb-0">
                  <i class="fa fa-clock me-2"></i>
                  <strong>Amaliyot muddati:</strong> 
                  {{ $student->internship_start_date->diffInDays($student->internship_end_date) }} kun
                </div>
              </div>
            @endif

            <div class="col-12"><hr></div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Qo'shilgan sana</label>
              <div class="fw-semibold">{{ $student->created_at->format('d.m.Y H:i') }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted small mb-1">Oxirgi yangilanish</label>
              <div class="fw-semibold">{{ $student->updated_at->format('d.m.Y H:i') }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions Card -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h5 class="card-title mb-3">
            <i class="fa fa-cog me-2 text-primary"></i>Amallar
          </h5>
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-warning">
              <i class="fa fa-edit me-2"></i>Tahrirlash
            </a>

            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger">
                <i class="fa fa-trash me-2"></i>O'chirish
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
