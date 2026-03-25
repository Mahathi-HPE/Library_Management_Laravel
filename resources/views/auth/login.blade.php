@extends('layout.app')

@section('page_title', 'Login')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-3">Login</h3>

                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="post" action="{{ route('auth.authenticate') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted">New user?</span>
                    <a href="{{ route('auth.register') }}">Register here</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
