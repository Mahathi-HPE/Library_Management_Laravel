@php($title = 'Member Dashboard')
@include('layout.header')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Member Dashboard</h3>
    <form method="post" action="{{ route('auth.logout') }}">
        @csrf
        <button class="btn btn-outline-danger" type="submit">Logout</button>
    </form>
</div>

<div class="list-group mb-4">
    <a href="{{ route('member.books') }}" class="list-group-item list-group-item-action">All Books</a>
    <a href="{{ route('member.current') }}" class="list-group-item list-group-item-action">Currently Borrowed Books</a>
    <a href="{{ route('member.returns') }}" class="list-group-item list-group-item-action">Return Books</a>
    <a href="{{ route('member.requests') }}" class="list-group-item list-group-item-action">My Requests</a>
    <a href="{{ route('member.history') }}" class="list-group-item list-group-item-action">History</a>
</div>

@include('layout.footer')
