<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRundown extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event_rundowns';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'waktu_mulai',
        'waktu_selesai',
        'judul_sesi',
        'pengisi_acara',
        'deskripsi_sesi',
        'urutan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'urutan' => 'integer',
        'created_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
