<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    protected $table = 'Books';

    protected $primaryKey = 'Bid';

    public $timestamps = false;

    protected $fillable = [
        'Title',
        'PubDate',
        'Price',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'BookAuthor', 'Bid', 'Aid', 'Bid', 'Aid');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class, 'Bid', 'Bid');
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class, 'Bid', 'Bid');
    }

    public static function availableBooks(?string $search = null)
    {
        return self::query()
            ->select('Books.Bid', 'Books.Title', 'Books.PubDate', 'Books.Price')
            ->selectSub(function ($query): void {
                $query->from('Copies')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('Copies.Bid', 'Books.Bid')
                    ->where('Copies.Status', 'Available')
                    ->whereNotExists(function ($sub): void {
                        $sub->from('Borrows')
                            ->whereColumn('Borrows.Cid', 'Copies.Cid')
                            ->where(function ($active): void {
                                $active->where('Borrows.BorrowStatus', 'Pending')
                                    ->orWhere(function ($approved): void {
                                        $approved->where('Borrows.BorrowStatus', 'Approved')
                                            ->where('Borrows.ReturnStatus', '!=', 'Approved');
                                    });
                            })
                            ->selectRaw('1');
                    });
            }, 'AvailableCopies')
            ->selectSub(function ($query): void {
                $query->from('BookAuthor')
                    ->join('Author', 'Author.Aid', '=', 'BookAuthor.Aid')
                    ->selectRaw('GROUP_CONCAT(DISTINCT Author.AuthName ORDER BY Author.AuthName SEPARATOR ", ")')
                    ->whereColumn('BookAuthor.Bid', 'Books.Bid');
            }, 'AuthName')
            ->when($search, function ($query, $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('Books.Title', 'like', '%' . $search . '%')
                        ->orWhereExists(function ($exists) use ($search): void {
                            $exists->from('BookAuthor')
                                ->join('Author', 'Author.Aid', '=', 'BookAuthor.Aid')
                                ->whereColumn('BookAuthor.Bid', 'Books.Bid')
                                ->where('Author.AuthName', 'like', '%' . $search . '%')
                                ->selectRaw('1');
                        });
                });
            })
            ->orderBy('Books.Title')
            ->get();
    }

    public static function addBookWithAuthorsAndCopies(array $data, array $authors): void
    {
        DB::transaction(function () use ($data, $authors): void {
            $book = self::query()->create([
                'Title' => $data['title'],
                'PubDate' => $data['pubdate'],
                'Price' => $data['price'],
            ]);

            foreach ($authors as $authorPayload) {
                if ($authorPayload['email'] !== '') {
                    $author = Author::query()->firstOrCreate(
                        ['AuthEmail' => $authorPayload['email']],
                        ['AuthName' => $authorPayload['name'], 'AuthLoc' => $authorPayload['location']]
                    );
                } else {
                    $author = Author::query()->firstOrCreate(
                        ['AuthName' => $authorPayload['name'], 'AuthLoc' => $authorPayload['location']],
                        ['AuthEmail' => null]
                    );
                }

                DB::table('BookAuthor')->updateOrInsert([
                    'Bid' => $book->Bid,
                    'Aid' => $author->Aid,
                ]);
            }

            Copy::createCopies($book->Bid, (int) $data['copies']);
        });
    }
}
