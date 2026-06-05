<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'mp_subscription_id', 'mp_payer_id',
        'status', 'plan_type', 'plan_amount', 'extra_emails',
        'email_limit', 'next_billing_date',
    ];

    protected function casts(): array
    {
        return [
            'plan_amount' => 'decimal:2',
            'next_billing_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'authorized';
    }

    public function isCorporate(): bool
    {
        return $this->plan_type === 'corporate';
    }

    public function monthlyAmount(): float
    {
        if ($this->isCorporate()) return 149.90;
        return 29.90 + ($this->extra_emails * 8.90);
    }

    public function emailLimit(): int
    {
        return $this->isCorporate() ? 20 : 6;
    }
}
