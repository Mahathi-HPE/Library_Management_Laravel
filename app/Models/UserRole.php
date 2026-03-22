<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    protected $table = 'UserRole';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'Rid',
        'Uid',
    ];
}
