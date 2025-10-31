@extends('layouts.admin')

@section('title', $organization->name . ' - Talabalar')

@section('content')
<div class="container-fluid">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.catalogs.organizations') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fa fa-arrow-left me-1"></i> Orqaga
            </a>
            <h1 class="h3 mb-1 fw-bold">{{ $organization->name }}</h1>
            <p class="text-muted mb-0">Tashkilotdagi talabalar ro'yxati</p>
        </div>
        <div class="text-end">
            <div class="d-flex gap-3 align-items-center">
                <div class="badge bg-info fs-6">
                    <i class="fa fa-users me-2"></i>
                    {{ $students->total() }} ta talaba
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Tashkilot nomi:</h6>
                    <p class="fw-bold">{{ $organization->name }}</p>
                </div>
                @if($organization->address)
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Manzil:</h6>
                    <p>{{ $organization->address }}</p>
                </div>
                @endif
                @if($organization->phone)
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Telefon:</h6>
                    <p>{{ $organization->phone }}</p>
                </div>
                @endif
                @if($organization->email)
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Email:</h6>
                    <p>{{ $organization->email }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>F.I.Sh</th>
                            <th>Guruh</th>
                            <th>Fakultet</th>
                            <th>Rahbar</th>
                            <th>Amaliyot muddati</th>
                            <th>Holat</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                                <td>
                                    <strong>{{ $student->full_name }}</strong>
                                </td>
                                <td>
                                    @if($student->group)
                                        <span class="badge bg-primary">{{ $student->group->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $student->faculty ?? '-' }}</td>
                                <td>
                                    @if($student->group && $student->group->supervisors->count() > 0)
                                        @foreach($student->group->supervisors as $supervisor)
                                            <span class="badge bg-info">{{ $supervisor->name }}</span>{{ !$loop->last ? ' ' : '' }}
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->internship_start_date && $student->internship_end_date)
                                        <small>
                                            {{ $student->internship_start_date->format('d.m.Y') }} - 
                                            {{ $student->internship_end_date->format('d.m.Y') }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->is_active)
                                        <span class="badge bg-success">Faol</span>
                                    @else
                                        <span class="badge bg-secondary">Nofaol</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.students.show', $student->id) }}" 
                                       class="btn btn-sm btn-outline-info"
                                       title="Ko'rish">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student->id) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Tahrirlash">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                    Bu tashkilotda talabalar yo'q
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="mt-3">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
