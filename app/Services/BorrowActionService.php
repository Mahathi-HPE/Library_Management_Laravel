<?php

namespace App\Services;

use App\Models\Borrow;

class BorrowActionService
{
    public function __construct(
        private readonly BorrowService $borrowService,
    ) {
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function approveBorrowRequest(int $borrowId): array
    {
        if ($borrowId <= 0) {
            return ['success' => false, 'message' => 'Invalid request.'];
        }

        if (!Borrow::approveBorrowRequest($borrowId)) {
            return ['success' => false, 'message' => 'Failed to approve request. Check availability.'];
        }

        $this->borrowService->invalidateAfterBorrowAction($borrowId);

        return ['success' => true, 'message' => 'Request approved successfully.'];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function rejectBorrowRequest(int $borrowId): array
    {
        if (!Borrow::rejectBorrowRequest($borrowId)) {
            return ['success' => false, 'message' => 'Invalid request.'];
        }

        $this->borrowService->invalidateAfterBorrowAction($borrowId);

        return ['success' => true, 'message' => 'Request rejected.'];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function approveReturnRequest(int $borrowId): array
    {
        if (!Borrow::approveReturnRequest($borrowId)) {
            return ['success' => false, 'message' => 'Invalid return.'];
        }

        $this->borrowService->invalidateAfterBorrowAction($borrowId);

        return ['success' => true, 'message' => 'Return approved successfully.'];
    }
}
