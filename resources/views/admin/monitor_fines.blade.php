@extends('layout.app')

@section('page_title', 'Monitor Fines')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Monitor Fines</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>MemName</th>
            <th>Title</th>
            <th>Copies</th>
            <th>Bdate</th>
            <th>Fine</th>
            <th>Fine Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>
                <td>{{ $row['MemName'] }}</td>
                <td>{{ $row['Title'] }}</td>
                <td>{{ (int) ($row['Copies'] ?? 0) }}</td>
                <td>{{ (string) ($row['Bdate'] ?? '') }}</td>
                <td>{{ number_format((float) $row['Fine'], 2) }}</td>
                <td>{{ $row['FineStatus'] }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No fine records found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
