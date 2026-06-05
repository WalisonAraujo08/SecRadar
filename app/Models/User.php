<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'cpf', 'phone', 'password',
        'is_active', 'agent_token', 'agent_last_seen_at', 'is_admin',
    ];

    protected $hidden = ['password', 'remember_token', 'agent_token'];

    protected function casts(): array
{
    return [
        'email_verified_at'     => 'datetime',
        'agent_last_seen_at'    => 'datetime',
        'partner_requested_at'  => 'datetime',
        'password'              => 'hashed',
        'is_active'             => 'boolean',
        'is_admin'              => 'boolean',
        'is_partner'            => 'boolean',
    ];
}

    // ── Relationships ──────────────────────────────────────────────────────

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function monitoredEmails()
    {
        return $this->hasMany(MonitoredEmail::class)->where('status', '!=', 'removed');
    }

    public function scanResults()
    {
        return $this->hasMany(ScanResult::class)->orderByDesc('detected_at');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class)->orderByDesc('created_at');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function hasActiveSubscription(): bool
    {
        return $this->subscription?->status === 'authorized';
    }

    public function unreadAlertsCount(): int
    {
        return $this->alerts()->where('read', false)->count();
    }

    public function generateAgentToken(): string
    {
        $token = Str::random(64);
        $this->update(['agent_token' => hash('sha256', $token)]);
        return $token; // retorna o token limpo uma única vez
    }

    public static function findByAgentToken(string $token): ?self
    {
        return static::where('agent_token', hash('sha256', $token))->first();
    }
   
    public function referrals()
    {
        return $this->hasMany(\App\Models\Referral::class, 'referrer_id');
    }
}
