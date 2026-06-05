<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoredEmail extends Model
{
    protected $fillable = [
        'user_id', 'email', 'is_primary', 'mp_item_id', 'last_scanned_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'last_scanned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scanResults(): HasMany
    {
        return $this->hasMany(ScanResult::class)->orderByDesc('detected_at');
    }
}
