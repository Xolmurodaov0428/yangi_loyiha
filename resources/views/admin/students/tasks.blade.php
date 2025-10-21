@extends('layouts.admin')

@section('title', 'Topshiriqlar')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">Topshiriqlar</h1>
      <p class="text-muted mb-0">Talabalar uchun topshiriqlarni boshqarish</p>
    </div>
    <button class="btn btn-primary" disabled>
      <i class="fa fa-plus me-2"></i>Yangi topshiriq
    </button>
  </div>

  <!-- Coming Soon -->
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="text-center py-5">
        <div class="mb-4">
          <i class="fa fa-tasks text-primary" style="font-size: 5rem; opacity: 0.3;"></i>
        </div>
        <h4 class="fw-bold mb-3">Tez orada...</h4>
        <p class="text-muted mb-4">
          Topshiriqlar moduli hozirda ishlab chiqilmoqda.<br>
          Bu yerda talabalar uchun topshiriqlar yaratish va boshqarish imkoniyati bo'ladi.
        </p>
        <div class="row g-3 justify-content-center">
          <div class="col-md-3">
            <div class="card border-0 bg-light">
              <div class="card-body text-center">
                <i class="fa fa-file-alt text-primary fs-3 mb-2"></i>
                <h6 class="fw-bold mb-1">Topshiriq yaratish</h6>
                <small class="text-muted">Yangi topshiriqlar qo'shish</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 bg-light">
              <div class="card-body text-center">
                <i class="fa fa-check-circle text-success fs-3 mb-2"></i>
                <h6 class="fw-bold mb-1">Baholash</h6>
                <small class="text-muted">Topshiriqlarni tekshirish</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 bg-light">
              <div class="card-body text-center">
                <i class="fa fa-chart-bar text-warning fs-3 mb-2"></i>
                <h6 class="fw-bold mb-1">Statistika</h6>
                <small class="text-muted">Natijalarni ko'rish</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
