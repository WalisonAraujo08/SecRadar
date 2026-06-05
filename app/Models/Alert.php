<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = ['user_id', 'scan_result_id', 'severity', 'read', 'seen_by_agent'];

    protected function casts(): array
    {
        return [
            'read' => 'boolean',
            'seen_by_agent' => 'boolean',
        ];
    }

    protected $attributes = [
        'read' => false,
        'seen_by_agent' => false,
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scanResult(): BelongsTo { return $this->belongsTo(ScanResult::class); }
}