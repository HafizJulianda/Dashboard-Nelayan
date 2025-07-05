<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DimBerat;
use App\Models\DimWaktu;

class FaktaNelayan extends Model
{
    use HasFactory;

    protected $table = 'fakta_nelayan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_waktu',
        'id_berat'
    ];

    public function dimBerat()
    {
        return $this->belongsTo(DimBerat::class, 'id_berat', 'id');
    }

    public function dimWaktu()
    {
        return $this->belongsTo(DimWaktu::class, 'id_waktu', 'id');
    }
}
