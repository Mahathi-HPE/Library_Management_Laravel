<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Services\BorrowActionService;
use App\Services\BorrowService;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly BookService $bookService,
        private readonly BorrowService $borrowService,
        private readonly BorrowActionService $borrowActionService,
        private readonly MemberService $memberService,
    ) {
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function manageUsers()
    {
        $rows = $this->memberService->adminManageUsersTable();

        return view('admin.manage_users', compact('rows'));
    }

    public function addBook(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.add_book');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'authors' => ['required', 'string'],
            'author_locations' => ['nullable', 'string'],
            'author_emails' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'gt:0'],
            'pubdate' => ['required', 'date'],
            'copies' => ['required', 'integer', 'gt:0'],
        ]);

        $result = $this->bookService->addBook($data);

        if (!$result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        return redirect()->route('admin.dashboard')->with('message', 'Book added successfully.');
    }

    public function monitorFines()
    {
        $rows = $this->borrowService->adminFineTable();

        return view('admin.monitor_fines', compact('rows'));
    }

    public function manageRequests()
    {
        $requests = $this->borrowService->pendingBorrowRequests();

        return view('admin.manage_requests', compact('requests'));
    }

    public function approveRequest(Request $request): RedirectResponse
    {
        $borrowId = (int) $request->input('borrow_id');
        $result = $this->borrowActionService->approveBorrowRequest($borrowId);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('message', $result['message']);
    }

    public function rejectRequest(Request $request): RedirectResponse
    {
        $borrowId = (int) $request->input('borrow_id');
        $result = $this->borrowActionService->rejectBorrowRequest($borrowId);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('message', $result['message']);
    }

    public function manageReturns()
    {
        $returns = $this->borrowService->pendingReturnRequests();

        return view('admin.manage_returns', compact('returns'));
    }

    public function approveReturn(Request $request): RedirectResponse
    {
        $borrowId = (int) $request->input('borrow_id');
        $result = $this->borrowActionService->approveReturnRequest($borrowId);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('message', $result['message']);
    }
}
