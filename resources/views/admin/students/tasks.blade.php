@extends('layouts.admin')

@section('title', 'Topshiriqlar')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">
        <i class="fas fa-tasks me-2 text-primary"></i>Topshiriqlar
      </h1>
      <p class="text-muted mb-0">Talabalar uchun topshiriqlarni boshqarish</p>
    </div>
    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary shadow-sm">
      <i class="fa fa-plus me-2"></i>Yangi topshiriq
    </a>
  </div>

  <!-- Info Section -->
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
      <div class="alert alert-info rounded-3 mb-0">
        <div class="d-flex">
          <div class="me-3">
            <i class="fas fa-info-circle fa-2x text-info"></i>
          </div>
          <div>
            <h5 class="alert-heading">Topshiriqlar boshqaruvi</h5>
            <p class="mb-0">Bu yerda tizimdagi barcha topshiriqlarni ko'rish va boshqarish mumkin.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
              <i class="fas fa-file-alt text-primary"></i>
            </div>
            <div>
              <h5 class="mb-0">24</h5>
              <p class="text-muted mb-0 small">Jami topshiriqlar</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
              <i class="fas fa-check-circle text-success"></i>
            </div>
            <div>
              <h5 class="mb-0">18</h5>
              <p class="text-muted mb-0 small">Yakunlangan</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
              <i class="fas fa-clock text-warning"></i>
            </div>
            <div>
              <h5 class="mb-0">6</h5>
              <p class="text-muted mb-0 small">Faol</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tasks List -->
  <div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
      <h5 class="card-title mb-4">
        <i class="fas fa-list me-2 text-primary"></i>Oxirgi topshiriqlar
      </h5>
      
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Nomi</th>
              <th>Rahbar</th>
              <th>Guruh</th>
              <th>Muddati</th>
              <th>Holati</th>
              <th class="text-end">Harakatlar</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <i class="fas fa-file-alt text-primary me-2"></i>
                  <span>Amaliyot hisoboti</span>
                </div>
              </td>
              <td>Ali Valiyev</td>
              <td><span class="badge bg-light text-dark">222-20</span></td>
              <td>15.11.2025 17:00</td>
              <td><span class="badge bg-success">Yakunlangan</span></td>
              <td class="text-end">
                <a href="#" class="btn btn-sm btn-outline-primary">Ko'rish</a>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <i class="fas fa-file-alt text-primary me-2"></i>
                  <span>Kurs ishi</span>
                </div>
              </td>
              <td>Nodira To'xtayeva</td>
              <td><span class="badge bg-light text-dark">315-19</span></td>
              <td>20.11.2025 18:00</td>
              <td><span class="badge bg-warning">Faol</span></td>
              <td class="text-end">
                <a href="#" class="btn btn-sm btn-outline-primary">Ko'rish</a>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <i class="fas fa-file-alt text-primary me-2"></i>
                  <span>Malaka ishi</span>
                </div>
              </td>
              <td>Jamshid Karimov</td>
              <td><span class="badge bg-light text-dark">401-21</span></td>
              <td>25.11.2025 16:00</td>
              <td><span class="badge bg-primary">Faol</span></td>
              <td class="text-end">
                <a href="#" class="btn btn-sm btn-outline-primary">Ko'rish</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-primary shadow-sm">
          <i class="fa fa-tasks me-2"></i>Barcha topshiriqlarni ko'rish
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }
  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
  }
</style>
@endpush