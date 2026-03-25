@extends('layout.app')

@section('page_title', 'Borrow History')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Borrow History</h3>
    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Back</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Title</th>
            <th>Price</th>
            <th>Author Name</th>
            <th>Copies</th>
            <th>Bdate</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>
                <td>{{ $row->Title }}</td>
                <td>{{ number_format((float) $row->Price, 2) }}</td>
                <td>{{ $row->AuthName ?? '' }}</td>
                <td>{{ (int) $row->Copies }}</td>
                <td>{{ $row->Bdate ?? '' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center">No history found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
