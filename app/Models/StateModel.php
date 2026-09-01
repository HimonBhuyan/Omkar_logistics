<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateModel extends Model
{
    protected $table = 'states';
    protected $fillable = ['country_id', 'name', 'code', 'short_name'];

    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities()
    {
        return $this->hasMany(CityModel::class, 'state_id');
    }
}
