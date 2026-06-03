<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    protected $fillable = ['nombre', 'ubicacion'];

    public function cultivos()
    {
        return $this->hasMany(Cultivo::class);
    }
}
