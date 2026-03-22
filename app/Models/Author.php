<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    protected $table = 'Author';

    protected $primaryKey = 'Aid';

    public $timestamps = false;

    protected $fillable = [
        'AuthLoc',
        'AuthEmail',
        'AuthName',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'BookAuthor', 'Aid', 'Bid', 'Aid', 'Bid');
    }
}
