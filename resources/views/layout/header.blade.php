@php
    $title = $title ?? 'Library Management';
    $user = auth()->user();
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Library Management</a>
        @if ($user)
            <span class="navbar-text text-white">
                {{ $user->Username }} ({{ session('role', 'User') }})
            </span>
        @endif
    </div>
</nav>
<main class="container">
