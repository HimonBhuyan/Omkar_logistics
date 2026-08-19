<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupLedger extends Model
{
    protected $table = 'group_ledgers';

    protected $fillable = ['name', 'sort_order'];
}
