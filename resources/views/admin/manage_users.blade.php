@extends('layout.app')

@section('page_title', 'Manage Users')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Users</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>MemName</th>
            <th>Title</th>
            <th>Price</th>
            <th>AuthName</th>
            <th>Copies</th>
            <th>Bdate</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>
                <td>{{ data_get($row, 'MemName', '') }}</td>
                <td>{{ data_get($row, 'Title', '') }}</td>
                <td>{{ number_format((float) data_get($row, 'Price', 0), 2) }}</td>
                <td>{{ data_get($row, 'AuthName', '') }}</td>
                <td>{{ (int) data_get($row, 'Copies', 0) }}</td>
                <td>{{ (string) data_get($row, 'Bdate', '') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No rows found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
