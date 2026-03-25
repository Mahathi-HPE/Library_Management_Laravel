<?php

namespace App\Services;

use App\Models\Borrow;
use App\Models\Copy;
use App\Services\LibraryCacheService;
use Illuminate\Support\Facades\Cache;

class BorrowService
{
    public function __construct(
        private readonly LibraryCacheService $cacheService,
    ) {
    }

    public function borrowedThisMonth(int $mid): int
    {
        return Cache::remember(
            $this->cacheService->borrowedThisMonthKey($mid),
            now()->endOfMonth()->endOfDay(),
            static fn () => Borrow::borrowedThisMonth($mid)
        );
    }

    public function adminFineTable()
    {
        return Borrow::adminFineTable();
    }

    public function pendingBorrowRequests()
    {
        return Borrow::pendingBorrowRequests();
    }

    public function pendingReturnRequests()
    {
        return Borrow::pendingReturnRequests();
    }

    public function currentBorrowedByMember(int $mid)
    {
        return Cache::remember(
            $this->cacheService->currentBorrowsKey($mid),
            now()->addMinutes(5),
            static fn () => Borrow::currentBorrowedByMember($mid)
        );
    }

    public function historyByMember(int $mid)
    {
        return Borrow::historyByMember($mid);
    }

    public function requestRowsByMember(int $mid)
    {
        return Borrow::requestRowsByMember($mid);
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function requestBook(int $mid, int $bid, int $quantity): array
    {
        $availableCopyIds = Copy::findAvailableCopyIds($bid, $quantity);

        if (count($availableCopyIds) < $quantity) {
            return ['success' => false, 'message' => 'Not enough available copies.'];
        }

        $borrowedThisMonth = $this->borrowedThisMonth($mid);

        if (($borrowedThisMonth + $quantity) > 7) {
            $remaining = max(0, 7 - $borrowedThisMonth);
            return ['success' => false, 'message' => "Only {$remaining} requests left this month."];
        }

        Borrow::createPendingForCopies($availableCopyIds, $mid, $bid);
        $this->cacheService->invalidateAvailableBooks();
        $this->cacheService->invalidateBorrowedThisMonth($mid);
        $this->cacheService->invalidateCurrentBorrows($mid);

        return [
            'success' => true,
            'message' => 'Book request submitted successfully. Waiting for admin approval.',
        ];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function requestReturn(int $mid, int $bid, int $quantity): array
    {
        $borrowIds = Borrow::findReturnableBorrowIds($mid, $bid, $quantity);

        if (count($borrowIds) < $quantity) {
            return ['success' => false, 'message' => 'Requested number of copies cannot be returned.'];
        }

        Borrow::markReturnPending($borrowIds);
        $this->cacheService->invalidateAvailableBooks();
        $this->cacheService->invalidateCurrentBorrows($mid);

        return [
            'success' => true,
            'message' => 'Return request submitted successfully. Waiting for admin approval.',
        ];
    }

    public function invalidateAfterBorrowAction(int $borrowId): void
    {
        $this->cacheService->invalidateAvailableBooks();

        $borrow = Borrow::query()->find($borrowId);
        if (!$borrow) {
            return;
        }

        $mid = (int) $borrow->Mid;
        $this->cacheService->invalidateBorrowedThisMonth($mid);
        $this->cacheService->invalidateCurrentBorrows($mid);
    }
}
