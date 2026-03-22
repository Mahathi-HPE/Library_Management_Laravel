@php($title = 'My Requests')
@include('layout.header')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>My Requests</h3>
    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Back</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead>
        <tr>
            <th>Title</th>
            <th>Request ID</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($requests as $row)
            <tr>
                <td>{{ data_get($row, 'Title', '') }}</td>
                <td>#{{ (int) data_get($row, 'BorrowId', 0) }}</td>
                <td>
                    @php($status = (string) data_get($row, 'Status', ''))
                    @if ($status === 'Pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif ($status === 'Approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif ($status === 'Rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-secondary">{{ $status !== '' ? $status : 'Unknown' }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center">No requests found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@include('layout.footer')
