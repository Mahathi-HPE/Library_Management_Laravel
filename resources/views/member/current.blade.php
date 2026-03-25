@extends('layout.app')

@section('page_title', 'Currently Borrowed Books')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Currently Borrowed Books</h3>
    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Back</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Title</th>
            <th>Bdate</th>
            <th>Price</th>
            <th>Author Name</th>
            <th>Copies</th>
            <th>Fine</th>
            <th>Fine Status</th>
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
                <td>{{ number_format((float) $row->Fine, 2) }}</td>
                <td>{{ $row->FineStatus }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center">No current borrowed books.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
