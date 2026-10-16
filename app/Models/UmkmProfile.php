<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UmkmProfile extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'umkm_profiles';

    public $timestamps = false;

    protected $fillable = [
        'nik',
        'no_kk',
        'nama_pemilik',
        'nama_usaha',
        'nomor_wa',
        'kecamatan',
        'alamat_usaha',
        'nib',
        'has_halal',
        'has_pirt',
        'pernah_terima_hibah',
        'tahun_terima_hibah',
    ];

    protected $casts = [
        'has_halal' => 'boolean',
        'has_pirt' => 'boolean',
        'pernah_terima_hibah' => 'boolean',
        'tahun_terima_hibah' => 'integer',
        'created_at' => 'datetime',
    ];

    public function stands(): HasMany
    {
        return $this->hasMany(EventStand::class, 'umkm_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SpkUmkmEvaluation::class, 'umkm_id');
    }
}
