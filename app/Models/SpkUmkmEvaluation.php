<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkUmkmEvaluation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'spk_umkm_evaluations';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'umkm_id',
        'stand_id',
        'score_konsistensi_transaksi',
        'score_digitalisasi_qris',
        'score_kepatuhan_regulasi',
        'skor_akhir_spk',
        'cluster_kategori',
        'rekomendasi_program',
        'is_prioritas_hibah_200',
        'catatan_dinas',
        'calculated_at',
    ];

    protected $casts = [
        'score_konsistensi_transaksi' => 'decimal:4',
        'score_digitalisasi_qris' => 'decimal:4',
        'score_kepatuhan_regulasi' => 'decimal:2',
        'skor_akhir_spk' => 'decimal:2',
        'is_prioritas_hibah_200' => 'boolean',
        'calculated_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(EventStand::class, 'stand_id');
    }

    public function umkmProfile(): BelongsTo
    {
        return $this->belongsTo(UmkmProfile::class, 'umkm_id');
    }
}
