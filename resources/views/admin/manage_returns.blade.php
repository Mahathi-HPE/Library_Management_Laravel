@extends('layout.app')

@section('page_title', 'Manage Return Requests')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Return Requests</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back</a>
</div>

@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead>
        <tr>
            <th>Title</th>
            <th>Member</th>
            <th>Borrow ID</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($returns as $row)
            <tr>
                <td>{{ $row['Title'] }}</td>
                <td>{{ $row['MemName'] }}</td>
                <td>#{{ (int) $row['BorrowId'] }}</td>
                <td>
                    <form method="post" action="{{ route('admin.approveReturn') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="borrow_id" value="{{ (int) $row['BorrowId'] }}">
                        <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center">No pending returns.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
