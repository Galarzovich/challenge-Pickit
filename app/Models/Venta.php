<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'venta';
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
        return $this->hasMany(DetalleVenta::class,'venta_id');
    }


}
