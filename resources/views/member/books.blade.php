@php($title = 'All Books')
@include('layout.header')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>All Books</h3>
    <div class="text-end">
        <p class="mb-0"><small class="text-muted">Monthly limit: <strong>{{ (int) $borrowedThisMonth }}/7</strong> | Remaining: <strong>{{ (int) $remainingThisMonth }}</strong></small></p>
        <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="get" action="{{ route('member.books') }}" class="row g-2 mb-3">
    <div class="col-md-8">
        <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}" placeholder="Search by title or author">
    </div>
    <div class="col-md-4">
        <button class="btn btn-primary w-100">Search</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead>
        <tr>
            <th>Title</th>
            <th>Author Name</th>
            <th>Price</th>
            <th>Pub Date</th>
            <th>Available Copies</th>
            <th>Borrow</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($books as $row)
            <tr>
                <td>{{ $row['Title'] }}</td>
                <td>{{ $row['AuthName'] ?? '' }}</td>
                <td>{{ number_format((float) $row['Price'], 2) }}</td>
                <td>{{ $row['PubDate'] }}</td>
                <td>{{ (int) $row['AvailableCopies'] }}</td>
                <td>
                    <form method="post" action="{{ route('member.requestBook') }}" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="hidden" name="bid" value="{{ (int) $row['Bid'] }}">
                        <input
                            type="number"
                            name="quantity"
                            class="form-control form-control-sm"
                            min="1"
                            max="{{ (int) $row['AvailableCopies'] }}"
                            value="1"
                            style="width: 90px;"
                            required
                            {{ (int) $row['AvailableCopies'] <= 0 ? 'disabled' : '' }}
                        >
                        <button class="btn btn-sm btn-success" {{ (int) $row['AvailableCopies'] <= 0 ? 'disabled' : '' }}>Request</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No available books found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@include('layout.footer')
