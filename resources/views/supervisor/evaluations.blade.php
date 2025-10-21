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

            <!-- Future: Evaluations form and table -->
            <!--
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Talaba</th>
                                <th>Amaliyot Davri</th>
                                <th>Baho</th>
                                <th>Izoh</th>
                                <th>Sana</th>
                                <th>Harakatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->internship_start_date->format('d.m.Y') }} - {{ $student->internship_end_date->format('d.m.Y') }}</td>
                                    <td>
                                        <select class="form-select form-select-sm">
                                            <option>5 (A'lo)</option>
                                            <option>4 (Yaxshi)</option>
                                            <option>3 (Qoniqarli)</option>
                                            <option>2 (Qoniqarsiz)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" placeholder="Izoh kiriting">
                                    </td>
                                    <td>{{ now()->format('d.m.Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" title="Saqlash">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            -->
        </div>
    </div>
</div>
@endsection
