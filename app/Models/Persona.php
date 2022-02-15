<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    protected $guarded = [];

    protected $with = [
        'detalle'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function detalle()
    {
        return $this->hasMany(Vehiculo::class,'persona_id');
    }
}
