<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Services\BorrowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct(
        private readonly BookService $bookService,
        private readonly BorrowService $borrowService,
    ) {
    }

    public function dashboard()
    {
        return view('member.dashboard', [
            'user' => Auth::user(),
            'mid' => (int) session('mid', 0),
            'memName' => (string) session('mem_name', ''),
        ]);
    }

    public function books(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $mid = (int) session('mid', 0);

        $books = $this->bookService->availableBooks($search !== '' ? $search : null);

        $borrowedThisMonth = $this->borrowService->borrowedThisMonth($mid);

        $remainingThisMonth = max(0, 7 - $borrowedThisMonth);

        return view('member.books', [
            'books' => $books,
            'search' => $search,
            'borrowedThisMonth' => $borrowedThisMonth,
            'remainingThisMonth' => $remainingThisMonth,
        ]);
    }

    public function requestBook(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bid' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $mid = (int) session('mid', 0);
        $bid = (int) $data['bid'];
        $quantity = (int) $data['quantity'];
        $result = $this->borrowService->requestBook($mid, $bid, $quantity);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('member.books')->with('message', $result['message']);
    }

    public function current()
    {
        $rows = $this->borrowService->currentBorrowedByMember((int) session('mid', 0));

        return view('member.current', compact('rows'));
    }

    public function returns()
    {
        $rows = $this->borrowService->currentBorrowedByMember((int) session('mid', 0));

        return view('member.returns', compact('rows'));
    }

    public function requestReturn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bid' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $mid = (int) session('mid', 0);
        $bid = (int) $data['bid'];
        $quantity = (int) $data['quantity'];
        $result = $this->borrowService->requestReturn($mid, $bid, $quantity);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('member.returns')->with('message', $result['message']);
    }

    public function history()
    {
        $rows = $this->borrowService->historyByMember((int) session('mid', 0));

        return view('member.history', compact('rows'));
    }

    public function requests()
    {
        $requests = $this->borrowService->requestRowsByMember((int) session('mid', 0));

        return view('member.requests', compact('requests'));
    }
}
