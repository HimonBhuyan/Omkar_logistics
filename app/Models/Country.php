<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = ['name', 'code'];

    public function states()
    {
        return $this->hasMany(StateModel::class, 'country_id');
    }

    public function cities()
    {
        return $this->hasManyThrough(CityModel::class, StateModel::class, 'country_id', 'state_id');
    }
}
