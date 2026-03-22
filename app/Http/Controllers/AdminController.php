<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->ensureAdmin();

        return view('admin.dashboard');
    }

    public function manageUsers()
    {
        $this->ensureAdmin();

        $rows = Member::adminManageUsersTable();

        return view('admin.manage_users', compact('rows'));
    }

    public function addBook(Request $request)
    {
        $this->ensureAdmin();

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

        $authorNames = array_values(array_filter(array_map('trim', explode(',', $data['authors']))));
        $authorLocations = empty($data['author_locations'])
            ? []
            : array_values(array_map('trim', explode(',', $data['author_locations'])));
        $authorEmails = empty($data['author_emails'])
            ? []
            : array_values(array_map('trim', explode(',', $data['author_emails'])));

        if (!empty($authorLocations) && count($authorLocations) !== count($authorNames)) {
            return back()->with('error', 'Author locations must match the number of author names.')->withInput();
        }

        if (!empty($authorEmails) && count($authorEmails) !== count($authorNames)) {
            return back()->with('error', 'Author emails must match the number of author names.')->withInput();
        }

        $authors = [];
        foreach ($authorNames as $index => $name) {
            $email = $authorEmails[$index] ?? '';
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()->with('error', 'Please enter valid author emails.')->withInput();
            }

            $key = strtolower($name);
            if (!isset($authors[$key])) {
                $authors[$key] = [
                    'name' => $name,
                    'location' => $authorLocations[$index] ?? 'Unknown',
                    'email' => $email,
                ];
            }
        }

        Book::addBookWithAuthorsAndCopies($data, array_values($authors));

        return redirect()->route('admin.dashboard')->with('message', 'Book added successfully.');
    }

    public function monitorFines()
    {
        $this->ensureAdmin();

        $rows = Borrow::adminFineTable();

        return view('admin.monitor_fines', compact('rows'));
    }

    public function manageRequests()
    {
        $this->ensureAdmin();

        $requests = Borrow::pendingBorrowRequests();

        return view('admin.manage_requests', compact('requests'));
    }

    public function approveRequest(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $borrowId = (int) $request->input('borrow_id');
        if ($borrowId <= 0) {
            return back()->with('error', 'Invalid request.');
        }

        if (!Borrow::approveBorrowRequest($borrowId)) {
            return back()->with('error', 'Failed to approve request. Check availability.');
        }

        return back()->with('message', 'Request approved successfully.');
    }

    public function rejectRequest(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $borrowId = (int) $request->input('borrow_id');
        if (!Borrow::rejectBorrowRequest($borrowId)) {
            return back()->with('error', 'Invalid request.');
        }

        return back()->with('message', 'Request rejected.');
    }

    public function manageReturns()
    {
        $this->ensureAdmin();

        $returns = Borrow::pendingReturnRequests();

        return view('admin.manage_returns', compact('returns'));
    }

    public function approveReturn(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $borrowId = (int) $request->input('borrow_id');
        if (!Borrow::approveReturnRequest($borrowId)) {
            return back()->with('error', 'Invalid return.');
        }

        return back()->with('message', 'Return approved successfully.');
    }

    private function ensureAdmin(): void
    {
        abort_unless((string) session('role') === 'Admin', 403);
    }
}
