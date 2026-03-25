@extends('layout.app')

@section('page_title', 'Add New Book')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Add New Book</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back</a>
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

<form method="post" action="{{ route('admin.addBook') }}" class="card card-body">
    @csrf
    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Author Names (comma separated)</label>
        <input type="text" name="authors" class="form-control" placeholder="Author 1, Author 2" value="{{ old('authors') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Author Locations (comma separated, same order)</label>
        <input type="text" name="author_locations" class="form-control" placeholder="Location 1, Location 2" value="{{ old('author_locations') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Author Emails (comma separated, same order)</label>
        <input type="text" name="author_emails" class="form-control" placeholder="author1@mail.com, author2@mail.com" value="{{ old('author_emails') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="price" class="form-control" min="1" step="0.01" value="{{ old('price') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Pub Date</label>
        <input type="date" name="pubdate" class="form-control" value="{{ old('pubdate') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Number of Copies</label>
        <input type="number" name="copies" class="form-control" min="1" value="{{ old('copies', 1) }}" required>
    </div>
    <button class="btn btn-primary">Add Book</button>
</form>

@endsection
