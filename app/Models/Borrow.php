<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Borrow extends Model
{
    protected $table = 'Borrows';

    protected $primaryKey = 'BorrowId';

    public $timestamps = false;

    protected $fillable = [
        'Cid',
        'Mid',
        'Bid',
        'Bdate',
        'BorrowStatus',
        'Fine',
        'FineStatus',
        'ReturnStatus',
    ];

    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class, 'Cid', 'Cid');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'Mid', 'Mid');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'Bid', 'Bid');
    }

    public static function borrowedThisMonth(int $mid): int
    {
        return self::query()
            ->where('Mid', $mid)
            ->whereMonth('Bdate', Carbon::now()->month)
            ->whereYear('Bdate', Carbon::now()->year)
            ->whereIn('BorrowStatus', ['Pending', 'Approved'])
            ->count();
    }

    public static function createPendingForCopies(array $copyIds, int $mid, int $bid): void
    {
        DB::transaction(function () use ($copyIds, $mid, $bid): void {
            foreach ($copyIds as $cid) {
                self::query()->create([
                    'Cid' => $cid,
                    'Mid' => $mid,
                    'Bid' => $bid,
                    'Bdate' => Carbon::today()->toDateString(),
                    'BorrowStatus' => 'Pending',
                    'Fine' => 0,
                    'FineStatus' => 'NA',
                    'ReturnStatus' => 'Not Returned',
                ]);
            }
        });
    }

    public static function currentBorrowedByMember(int $mid)
    {
        $agg = self::query()
            ->from('Borrows as br')
            ->join('Copies as c', 'c.Cid', '=', 'br.Cid')
            ->where('br.Mid', $mid)
            ->where('br.BorrowStatus', 'Approved')
            ->where('br.ReturnStatus', 'Not Returned')
            ->groupBy('c.Bid')
            ->selectRaw('c.Bid, COUNT(DISTINCT c.Cid) AS Copies, MAX(br.Bdate) AS Bdate, SUM(br.Fine) AS Fine');

        return DB::query()
            ->fromSub($agg, 'agg')
            ->join('Books as b', 'b.Bid', '=', 'agg.Bid')
            ->leftJoin('BookAuthor as ba', 'ba.Bid', '=', 'b.Bid')
            ->leftJoin('Author as a', 'a.Aid', '=', 'ba.Aid')
            ->groupBy('agg.Bid', 'b.Title', 'b.Price', 'agg.Copies', 'agg.Bdate', 'agg.Fine')
            ->selectRaw('agg.Bid, b.Title, b.Price, GROUP_CONCAT(DISTINCT a.AuthName ORDER BY a.AuthName SEPARATOR ", ") AS AuthName, agg.Copies, agg.Bdate, agg.Fine, CASE WHEN agg.Fine > 0 THEN "Not Paid" ELSE "NA" END AS FineStatus')
            ->orderByDesc('agg.Bdate')
            ->orderBy('b.Title')
            ->get();
    }

    public static function historyByMember(int $mid)
    {
        $agg = self::query()
            ->from('Borrows as br')
            ->where('br.Mid', $mid)
            ->whereNotNull('br.Bid')
            ->groupBy('br.Bid')
            ->selectRaw('br.Bid, COUNT(*) AS Copies, MAX(br.Bdate) AS Bdate');

        return DB::query()
            ->fromSub($agg, 'agg')
            ->join('Books as b', 'b.Bid', '=', 'agg.Bid')
            ->leftJoin('BookAuthor as ba', 'ba.Bid', '=', 'b.Bid')
            ->leftJoin('Author as a', 'a.Aid', '=', 'ba.Aid')
            ->groupBy('agg.Bid', 'b.Title', 'b.Price', 'agg.Copies', 'agg.Bdate')
            ->selectRaw('agg.Bid, b.Title, b.Price, GROUP_CONCAT(DISTINCT a.AuthName ORDER BY a.AuthName SEPARATOR ", ") AS AuthName, agg.Copies, agg.Bdate')
            ->orderByDesc('agg.Bdate')
            ->orderBy('b.Title')
            ->get();
    }

    public static function requestRowsByMember(int $mid)
    {
        return self::query()
            ->join('Books', 'Books.Bid', '=', 'Borrows.Bid')
            ->where('Borrows.Mid', $mid)
            ->whereNotNull('Borrows.BorrowStatus')
            ->select(
                'Borrows.BorrowId',
                'Borrows.Bid',
                'Borrows.BorrowStatus as Status',
                'Books.Title'
            )
            ->orderByDesc('Borrows.BorrowId')
            ->get();
    }

    public static function findReturnableBorrowIds(int $mid, int $bid, int $limit): array
    {
        return self::query()
            ->where('Mid', $mid)
            ->where('Bid', $bid)
            ->where('BorrowStatus', 'Approved')
            ->where('ReturnStatus', 'Not Returned')
            ->orderBy('BorrowId')
            ->limit($limit)
            ->pluck('BorrowId')
            ->all();
    }

    public static function markReturnPending(array $borrowIds): void
    {
        self::query()->whereIn('BorrowId', $borrowIds)->update(['ReturnStatus' => 'Pending']);
    }

    public static function adminFineTable()
    {
        return self::query()
            ->join('Members', 'Members.Mid', '=', 'Borrows.Mid')
            ->join('Books', 'Books.Bid', '=', 'Borrows.Bid')
            ->where('Borrows.Fine', '>', 0)
            ->select(
                'Borrows.BorrowId',
                'Members.MemName',
                'Books.Title',
                'Borrows.Fine',
                'Borrows.FineStatus',
                'Borrows.BorrowStatus',
                'Borrows.ReturnStatus',
                'Borrows.Bdate'
            )
            ->orderByDesc('Borrows.BorrowId')
            ->get();
    }

    public static function pendingBorrowRequests()
    {
        return self::query()
            ->join('Members', 'Members.Mid', '=', 'Borrows.Mid')
            ->join('Books', 'Books.Bid', '=', 'Borrows.Bid')
            ->where('Borrows.BorrowStatus', 'Pending')
            ->select('Borrows.*', 'Members.MemName', 'Books.Title')
            ->orderBy('Borrows.BorrowId')
            ->get();
    }

    public static function pendingReturnRequests()
    {
        return self::query()
            ->join('Members', 'Members.Mid', '=', 'Borrows.Mid')
            ->join('Books', 'Books.Bid', '=', 'Borrows.Bid')
            ->where('Borrows.ReturnStatus', 'Pending')
            ->select('Borrows.*', 'Members.MemName', 'Books.Title')
            ->orderBy('Borrows.BorrowId')
            ->get();
    }

    public static function approveBorrowRequest(int $borrowId): bool
    {
        $borrow = self::query()->find($borrowId);
        if (!$borrow || $borrow->BorrowStatus !== 'Pending') {
            return false;
        }

        return DB::transaction(function () use ($borrow): bool {
            $copy = Copy::query()->find($borrow->Cid);
            if (!$copy || $copy->Status !== 'Available') {
                return false;
            }

            $copy->markRented();
            $borrow->BorrowStatus = 'Approved';
            $borrow->save();

            return true;
        });
    }

    public static function rejectBorrowRequest(int $borrowId): bool
    {
        $borrow = self::query()->find($borrowId);
        if (!$borrow || $borrow->BorrowStatus !== 'Pending') {
            return false;
        }

        $borrow->BorrowStatus = 'Rejected';
        $borrow->save();

        return true;
    }

    public static function approveReturnRequest(int $borrowId): bool
    {
        $borrow = self::query()->find($borrowId);
        if (!$borrow || $borrow->ReturnStatus !== 'Pending') {
            return false;
        }

        return DB::transaction(function () use ($borrow): bool {
            $copy = Copy::query()->find($borrow->Cid);
            if ($copy) {
                $copy->markAvailable();
            }

            $borrow->ReturnStatus = 'Approved';
            $borrow->save();

            return true;
        });
    }
}
