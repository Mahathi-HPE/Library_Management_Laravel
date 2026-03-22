<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Copy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function dashboard()
    {
        $this->ensureMember();

        return view('member.dashboard', [
            'user' => Auth::user(),
            'mid' => (int) session('mid', 0),
            'memName' => (string) session('mem_name', ''),
        ]);
    }

    public function books(Request $request)
    {
        $this->ensureMember();

        $search = trim((string) $request->query('search', ''));
        $mid = (int) session('mid', 0);

        $books = Book::availableBooks($search !== '' ? $search : null);

        $borrowedThisMonth = Borrow::borrowedThisMonth($mid);

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
        $this->ensureMember();

        $data = $request->validate([
            'bid' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $mid = (int) session('mid', 0);
        $bid = (int) $data['bid'];
        $quantity = (int) $data['quantity'];

        $availableCopyIds = Copy::findAvailableCopyIds($bid, $quantity);

        if (count($availableCopyIds) < $quantity) {
            return back()->with('error', 'Not enough available copies.');
        }

        $borrowedThisMonth = Borrow::borrowedThisMonth($mid);

        if (($borrowedThisMonth + $quantity) > 7) {
            $remaining = max(0, 7 - $borrowedThisMonth);
            return back()->with('error', "Only {$remaining} requests left this month.");
        }

        Borrow::createPendingForCopies($availableCopyIds, $mid, $bid);

        return redirect()->route('member.books')->with('message', 'Book request submitted successfully. Waiting for admin approval.');
    }

    public function current()
    {
        $this->ensureMember();

        $rows = Borrow::currentBorrowedByMember((int) session('mid', 0));

        return view('member.current', compact('rows'));
    }

    public function returns()
    {
        $this->ensureMember();

        $rows = Borrow::currentBorrowedByMember((int) session('mid', 0));

        return view('member.returns', compact('rows'));
    }

    public function requestReturn(Request $request): RedirectResponse
    {
        $this->ensureMember();

        $data = $request->validate([
            'bid' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $mid = (int) session('mid', 0);
        $bid = (int) $data['bid'];
        $quantity = (int) $data['quantity'];

        $borrowIds = Borrow::findReturnableBorrowIds($mid, $bid, $quantity);

        if (count($borrowIds) < $quantity) {
            return back()->with('error', 'Requested number of copies cannot be returned.');
        }

        Borrow::markReturnPending($borrowIds);

        return redirect()->route('member.returns')->with('message', 'Return request submitted successfully. Waiting for admin approval.');
    }

    public function history()
    {
        $this->ensureMember();

        $rows = Borrow::historyByMember((int) session('mid', 0));

        return view('member.history', compact('rows'));
    }

    public function requests()
    {
        $this->ensureMember();

        $requests = Borrow::requestRowsByMember((int) session('mid', 0));

        return view('member.requests', compact('requests'));
    }

    private function ensureMember(): void
    {
        abort_unless((string) session('role') === 'User', 403);
    }
}
