@php($title = 'Manage Borrow Requests')
@include('layout.header')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Borrow Requests</h3>
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
            <th>Request ID</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($requests as $row)
            <tr>
                <td>{{ $row['Title'] }}</td>
                <td>{{ $row['MemName'] }}</td>
                <td>#{{ (int) $row['BorrowId'] }}</td>
                <td>
                    <form method="post" action="{{ route('admin.approveRequest') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="borrow_id" value="{{ (int) $row['BorrowId'] }}">
                        <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form method="post" action="{{ route('admin.rejectRequest') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="borrow_id" value="{{ (int) $row['BorrowId'] }}">
                        <button class="btn btn-sm btn-danger">Reject</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center">No pending requests.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@include('layout.footer')
