@php($title = 'Return Books')
@include('layout.header')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Return Books</h3>
    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Back</a>
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

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead>
        <tr>
            <th>Title</th>
            <th>Bdate</th>
            <th>Price</th>
            <th>Author Name</th>
            <th>Copies</th>
            <th>Return</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>
                <td>{{ $row->Title }}</td>
                <td>{{ (string) ($row->Bdate ?? '') }}</td>
                <td>{{ number_format((float) $row->Price, 2) }}</td>
                <td>{{ $row->AuthName ?? '' }}</td>
                <td>{{ (int) $row->Copies }}</td>
                <td>
                    <form method="post" action="{{ route('member.requestReturn') }}" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="hidden" name="bid" value="{{ (int) $row->Bid }}">
                        <input
                            type="number"
                            name="quantity"
                            class="form-control form-control-sm"
                            min="1"
                            max="{{ (int) $row->Copies }}"
                            value="1"
                            style="width: 90px;"
                            required
                        >
                        <button class="btn btn-sm btn-warning">Request Return</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No borrowed books available to return.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@include('layout.footer')
