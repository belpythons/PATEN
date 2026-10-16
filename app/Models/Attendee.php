<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'attendees';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'nama_lengkap',
        'nomor_wa',
        'kecamatan',
        'nomor_tiket_doorprize',
        'tipe_registrasi',
        'is_checked_in',
        'check_in_time',
        'is_doorprize_winner',
    ];

    protected $casts = [
        'is_checked_in' => 'boolean',
        'is_doorprize_winner' => 'boolean',
        'check_in_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
