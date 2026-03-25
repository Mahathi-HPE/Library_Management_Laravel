<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class LibraryCacheService
{
    private const AVAILABLE_BOOKS_VERSION_KEY = 'library:available_books:version';

    public function availableBooksKey(): string
    {
        $version = $this->availableBooksVersion();

        return sprintf('library:available_books:v%d:all', $version);
    }

    public function invalidateAvailableBooks(): void
    {
        Cache::increment(self::AVAILABLE_BOOKS_VERSION_KEY);
    }

    public function borrowedThisMonthKey(int $mid, ?Carbon $date = null): string
    {
        $current = $date ?? Carbon::now();

        return sprintf('library:borrowed_this_month:mid:%d:%s', $mid, $current->format('Y-m'));
    }

    public function currentBorrowsKey(int $mid): string
    {
        return sprintf('library:current_borrows:mid:%d', $mid);
    }

    public function invalidateBorrowedThisMonth(int $mid): void
    {
        Cache::forget($this->borrowedThisMonthKey($mid));
    }

    public function invalidateCurrentBorrows(int $mid): void
    {
        Cache::forget($this->currentBorrowsKey($mid));
    }

    private function availableBooksVersion(): int
    {
        if (!Cache::has(self::AVAILABLE_BOOKS_VERSION_KEY)) {
            Cache::forever(self::AVAILABLE_BOOKS_VERSION_KEY, 1);
        }

        return (int) Cache::get(self::AVAILABLE_BOOKS_VERSION_KEY, 1);
    }
}
