<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'logo_path'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_companies');
    }

    public function bilties()
    {
        return $this->hasMany(Bilty::class);
    }
}
