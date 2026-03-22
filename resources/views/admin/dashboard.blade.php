@php($title = 'Admin Dashboard')
@include('layout.header')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Admin Dashboard</h3>
    <form method="post" action="{{ route('auth.logout') }}">
        @csrf
        <button class="btn btn-outline-danger" type="submit">Logout</button>
    </form>
</div>

@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="list-group mb-4">
    <a href="{{ route('admin.manageUsers') }}" class="list-group-item list-group-item-action">Manage Users</a>
    <a href="{{ route('admin.manageRequests') }}" class="list-group-item list-group-item-action">Manage Borrow Requests</a>
    <a href="{{ route('admin.manageReturns') }}" class="list-group-item list-group-item-action">Manage Return Requests</a>
    <a href="{{ route('admin.addBook') }}" class="list-group-item list-group-item-action">Add New Book</a>
    <a href="{{ route('admin.monitorFines') }}" class="list-group-item list-group-item-action">Monitor Fines</a>
</div>

@include('layout.footer')
