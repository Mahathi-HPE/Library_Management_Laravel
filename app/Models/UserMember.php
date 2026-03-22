<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserMember extends Pivot
{
    protected $table = 'UserMember';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'Uid',
        'Mid',
    ];
}
