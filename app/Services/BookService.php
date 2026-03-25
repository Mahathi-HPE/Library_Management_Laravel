<?php

namespace App\Services;

use App\Models\Book;
use App\Services\LibraryCacheService;
use Illuminate\Support\Facades\Cache;

class BookService
{
    public function __construct(
        private readonly LibraryCacheService $cacheService,
    ) {
    }

    public function availableBooks(?string $search = null)
    {
        $normalizedSearch = trim((string) $search);
        $querySearch = $normalizedSearch === '' ? null : $normalizedSearch;

        if ($querySearch !== null) {
            return Book::availableBooks($querySearch);
        }

        return Cache::remember(
            $this->cacheService->availableBooksKey(),
            now()->addMinutes(30),
            static fn () => Book::availableBooks(null)
        );
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function addBook(array $data): array
    {
        $authorNames = array_values(array_filter(array_map('trim', explode(',', $data['authors']))));
        $authorLocations = empty($data['author_locations'])
            ? []
            : array_values(array_map('trim', explode(',', $data['author_locations'])));
        $authorEmails = empty($data['author_emails'])
            ? []
            : array_values(array_map('trim', explode(',', $data['author_emails'])));

        if (!empty($authorLocations) && count($authorLocations) !== count($authorNames)) {
            return ['success' => false, 'message' => 'Author locations must match the number of author names.'];
        }

        if (!empty($authorEmails) && count($authorEmails) !== count($authorNames)) {
            return ['success' => false, 'message' => 'Author emails must match the number of author names.'];
        }

        $authors = [];
        foreach ($authorNames as $index => $name) {
            $email = $authorEmails[$index] ?? '';
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Please enter valid author emails.'];
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
        $this->cacheService->invalidateAvailableBooks();

        return ['success' => true];
    }
}
