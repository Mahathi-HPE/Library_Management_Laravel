<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    protected $table = 'Members';

    protected $primaryKey = 'Mid';

    public $timestamps = false;

    protected $fillable = [
        'MemName',
        'MemEmail',
        'MemLoc',
    ];

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class, 'Mid', 'Mid');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'UserMember', 'Mid', 'Uid', 'Mid', 'Uid');
    }

    public static function findByUserId(int $uid): ?self
    {
        return self::query()
            ->join('UserMember', 'UserMember.Mid', '=', 'Members.Mid')
            ->where('UserMember.Uid', $uid)
            ->select('Members.*')
            ->first();
    }

    public static function adminManageUsersTable()
    {
        return self::query()
            ->from('Borrows as br')
            ->join('Members as m', 'm.Mid', '=', 'br.Mid')
            ->join('Copies as c', 'c.Cid', '=', 'br.Cid')
            ->join('Books as b', 'b.Bid', '=', 'c.Bid')
            ->leftJoin('BookAuthor as ba', 'ba.Bid', '=', 'b.Bid')
            ->leftJoin('Author as a', 'a.Aid', '=', 'ba.Aid')
            ->where('c.Status', 'Rented')
            ->where('br.ReturnStatus', '!=', 'Approved')
            ->groupBy('m.Mid', 'b.Bid', 'm.MemName', 'b.Title', 'b.Price')
            ->selectRaw('m.MemName, b.Title, b.Price, GROUP_CONCAT(DISTINCT a.AuthName ORDER BY a.AuthName SEPARATOR ", ") AS AuthName, COUNT(DISTINCT c.Cid) AS Copies, MAX(br.Bdate) AS Bdate')
            ->orderByDesc(DB::raw('MAX(br.Bdate)'))
            ->orderBy('m.MemName')
            ->orderBy('b.Title')
            ->get();
    }
}
