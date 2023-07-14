<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ['name', 'color'];

    public function colors()
    {
        return $this->belongsToMany(Item::class);
    }
}
