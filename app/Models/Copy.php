<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Copy extends Model
{
    protected $table = 'Copies';

    protected $primaryKey = 'Cid';

    public $timestamps = false;

    protected $fillable = [
        'Bid',
        'Status',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'Bid', 'Bid');
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class, 'Cid', 'Cid');
    }

    public static function findAvailableCopyIds(int $bid, int $limit): array
    {
        return self::query()
            ->where('Bid', $bid)
            ->where('Status', 'Available')
            ->whereNotExists(function ($query): void {
                $query->from('Borrows')
                    ->whereColumn('Borrows.Cid', 'Copies.Cid')
                    ->where(function ($active): void {
                        $active->where('Borrows.BorrowStatus', 'Pending')
                            ->orWhere(function ($approved): void {
                                $approved->where('Borrows.BorrowStatus', 'Approved')
                                    ->where('Borrows.ReturnStatus', '!=', 'Approved');
                            });
                    })
                    ->selectRaw('1');
            })
            ->limit($limit)
            ->pluck('Cid')
            ->all();
    }

    public static function createCopies(int $bid, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            self::query()->create([
                'Bid' => $bid,
                'Status' => 'Available',
            ]);
        }
    }

    public function markRented(): void
    {
        $this->Status = 'Rented';
        $this->save();
    }

    public function markAvailable(): void
    {
        $this->Status = 'Available';
        $this->save();
    }
}
