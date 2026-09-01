<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    protected $table = 'cities';
    protected $fillable = ['state_id', 'name', 'short_name'];

    public function stateRelation()
    {
        return $this->belongsTo(StateModel::class, 'state_id');
    }
}
