@extends('layouts.admin')

@section('title', 'Barcha Talabalar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Barcha Talabalar</h1>
    <!-- <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-download"></i> Export
        </button>
    </div> -->
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Talabalar Ro'yxati</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ism-Familiya</th>
                                    <th>Guruh</th>
                                    <th>Rahbar ID</th>
                                    <th>Holati</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student_login as $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->group_name ?? 'Guruh belgilanmagan' }}</td>
                                    <td>
                                        @if($student->supervisior_id)
                                            <span class="badge bg-info">{{ $student->supervisior_id }}</span>
                                        @else
                                            <span class="text-muted">Rahbar belgilanmagan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->is_active)
                                        <span class="badge bg-success">Faol</span>
                                        @else
                                        <span class="badge bg-danger">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">Amallar mavjud emas</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <!-- Removed pagination as we're fetching all records -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection