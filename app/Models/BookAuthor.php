<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookAuthor extends Pivot
{
    protected $table = 'BookAuthor';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'Bid',
        'Aid',
    ];
}
