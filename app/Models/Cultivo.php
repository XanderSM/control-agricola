<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cultivo extends Model
{
    protected $fillable = ['parcela_id', 'producto', 'fecha_siembra', 'imagen'];

    public function parcela()
    {
        return $this->belongsTo(Parcela::class);
    }
}
