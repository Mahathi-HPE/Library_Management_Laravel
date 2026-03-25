@php
    $sectionTitle = trim($__env->yieldContent('page_title'));
    $title = $sectionTitle !== '' ? $sectionTitle : ($title ?? 'Library Management');
@endphp

@include('layout.header')

@yield('content')

@include('layout.footer')
