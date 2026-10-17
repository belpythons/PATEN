<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventStand extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event_stands';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'umkm_id',
        'nomor_stand',
        'kategori_jualan',
        'daya_listrik_watt',
        'fasilitas_detail',
        'pin_pos',
        'total_omzet_terekam',
        'total_transaksi_terekam',
    ];

    protected $casts = [
        'fasilitas_detail' => 'array',
        'daya_listrik_watt' => 'integer',
        'total_omzet_terekam' => 'decimal:2',
        'total_transaksi_terekam' => 'integer',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'nama_usaha',
        'nama_pemilik',
    ];

    public function getNamaUsahaAttribute(): ?string
    {
        return $this->umkmProfile?->nama_usaha;
    }

    public function getNamaPemilikAttribute(): ?string
    {
        return $this->umkmProfile?->nama_pemilik;
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function umkmProfile(): BelongsTo
    {
        return $this->belongsTo(UmkmProfile::class, 'umkm_id');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(StandMenu::class, 'stand_id');
    }

    public function posTransactions(): HasMany
    {
        return $this->hasMany(PosTransaction::class, 'stand_id');
    }

    public function spkEvaluation(): HasOne
    {
        return $this->hasOne(SpkUmkmEvaluation::class, 'stand_id');
    }
}
