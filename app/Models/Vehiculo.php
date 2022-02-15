<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculo';
    protected $guarded = [];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class,'persona_id','id');
    }


}
