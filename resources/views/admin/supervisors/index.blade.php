@extends('layouts.admin')

@section('title', 'Rahbarlar ro\'yxati')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3">Rahbarlar</h1>
      <a href="{{ route('admin.supervisors.create') }}" class="btn btn-primary">Yangi rahbar</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Ism</th>
            <th>Login</th>
            <th>Email</th>
            <th>Tasdiq</th>
            <th>Status</th>
            <th>Davomat huquqi</th>
            <th>Amallar</th>
          </tr>
        </thead>
        <tbody>
          @forelse($supervisors as $sup)
            <tr>
              <td>{{ $sup->id }}</td>
              <td>{{ $sup->name }}</td>
              <td>{{ $sup->username ?? '—' }}</td>
              <td>{{ $sup->email }}</td>
              <td>{!! $sup->approved_at ? '<span class="badge bg-success">Tasdiqlangan</span>' : '<span class="badge bg-secondary">Kutilmoqda</span>' !!}</td>
              <td>{!! $sup->is_active ? '<span class="badge bg-success">Faol</span>' : '<span class="badge bg-danger">O\'chiq</span>' !!}</td>
              <td>
                <form method="POST" action="{{ route('admin.supervisors.toggle-attendance', $sup) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm {{ $sup->can_mark_attendance ? 'btn-success' : 'btn-danger' }}">
                    <i class="fa {{ $sup->can_mark_attendance ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                    {{ $sup->can_mark_attendance ? 'Yoqilgan' : 'O\'chirilgan' }}
                  </button>
                </form>
              </td>
              <td class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.supervisors.show', $sup) }}" class="btn btn-sm btn-info text-white">Ko'rish</a>
                <a href="{{ route('admin.supervisors.edit', $sup) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                <form method="POST" action="{{ route('admin.supervisors.destroy', $sup) }}" onsubmit="return confirm('O\'chirsinmi?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">O'chirish</button>
                </form>
                <form method="POST" action="{{ route('admin.supervisors.reset-password', $sup) }}" onsubmit="return confirm('Ushbu rahbar uchun yangi vaqtinchalik parol yaratiladi. Davom etasizmi?')">
                  @csrf
                  <button class="btn btn-sm btn-outline-primary">Parolni tiklash</button>
                </form>
                @if(!$sup->approved_at)
                  <form method="POST" action="{{ route('admin.supervisors.approve', $sup) }}">
                    @csrf
                    <button class="btn btn-sm btn-success">Tasdiqlash</button>
                  </form>
                @endif
                @if($sup->is_active)
                  <form method="POST" action="{{ route('admin.supervisors.deactivate', $sup) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Faolligini o'chirish</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">Ma'lumot yo'q</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $supervisors->links() }}

    <div class="mt-4">
      <a href="{{ route('admin.dashboard') }}" class="btn btn-link">← Admin dashboard</a>
    </div>
</div>
@endsection
