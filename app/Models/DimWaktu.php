<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimWaktu extends Model
{
    protected $table = 'dim_waktu';
    protected $primaryKey = 'id';
    public $incrementing = true; // AUTO_INCREMENT aktif
    protected $keyType = 'int';  // ID bertipe INT
    public $timestamps = false;  // Tidak ada kolom created_at/updated_at

    protected $fillable = ['bulan', 'tahun'];

    // Relasi ke fakta_nelayan
    public function faktaNelayan()
    {
        return $this->hasMany(FaktaNelayan::class, 'id_waktu', 'id');
    }
}
