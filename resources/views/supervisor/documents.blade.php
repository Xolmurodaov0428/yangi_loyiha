@extends('layouts.supervisor')

@section('title', 'Baholash')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Talabalar Baholash</h1>
        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i>Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Evaluations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0">
                    <i class="fa fa-star me-2 text-danger"></i>Baholash Ro'yxati
                </h5>
                <div class="text-muted small">
                    Baholangan: 0 ta <!-- Placeholder -->
                </div>
            </div>

            <div class="text-center py-5 text-muted">
                <i class="fa fa-star fs-1 mb-3 opacity-25"></i>
                <p class="mb-0">Hozircha baholashlar yo'q</p>
                <small class="text-muted">Talabalarni baholash uchun bu yerda forma va jadval bo'ladi</small>
            </div>

        </div>
    </div>
</div>
@endsection
