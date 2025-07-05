<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimBerat extends Model
{
    use HasFactory;

    protected $table = 'dim_berat';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'berat'
    ];

    // Relasi one-to-many ke FaktaNelayan (FK = id_berat)
    public function faktaNelayan()
    {
        return $this->hasMany(FaktaNelayan::class, 'id_berat', 'id');
    }
}
