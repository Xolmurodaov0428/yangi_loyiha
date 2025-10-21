@extends('layouts.admin')

@section('title', 'Rahbarni tahrirlash')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Rahbarni tahrirlash</h1>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.supervisors.update', $supervisor) }}" class="row g-3">
      @csrf
      @method('PUT')
      <div class="col-md-6">
        <label class="form-label">Ism</label>
        <input type="text" name="name" value="{{ old('name', $supervisor->name) }}" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Login</label>
        <input type="text" name="username" value="{{ old('username', $supervisor->username) }}" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $supervisor->email) }}" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Parol (ixtiyoriy)</label>
        <input type="password" name="password" class="form-control" />
      </div>
      <div class="col-md-6 form-check mt-4">
        <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ $supervisor->is_active ? 'checked' : '' }} />
        <label class="form-check-label" for="active">Faol</label>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Saqlash</button>
        <a class="btn btn-secondary" href="{{ route('admin.supervisors.index') }}">Bekor qilish</a>
      </div>
    </form>
</div>
@endsection
