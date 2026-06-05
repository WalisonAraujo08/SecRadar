<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanResult extends Model
{
    protected $fillable = [
        'user_id', 'monitored_email_id', 'source_key', 'breach_name',
        'data_exposed', 'severity', 'detected_at', 'breach_date',
        'notified_email', 'notified_whatsapp',
    ];

    protected function casts(): array
    {
        return [
            'data_exposed' => 'array',
            'detected_at' => 'datetime',
            'breach_date' => 'date',
            'notified_email' => 'boolean',
            'notified_whatsapp' => 'boolean',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function monitoredEmail(): BelongsTo { return $this->belongsTo(MonitoredEmail::class); }
    public function alert(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Alert::class);
    }

    public function severityLabel(): string
    {
        return match($this->severity) {
            'critical' => 'Crítico',
            'high'     => 'Alto',
            'medium'   => 'Médio',
            'low'      => 'Baixo',
            default    => 'Desconhecido',
        };
    }

    public function severityColor(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'high'     => 'orange',
            'medium'   => 'yellow',
            'low'      => 'blue',
            default    => 'gray',
        };
    }

    public function dataExposedLabel(): string
    {
        $map = [
            'email'    => 'E-mail',
            'senha'    => 'Senha',
            'cpf'      => 'CPF',
            'telefone' => 'Telefone',
            'nome'     => 'Nome',
            'endereco' => 'Endereço',
        ];
        return collect($this->data_exposed)
            ->map(fn($k) => $map[$k] ?? $k)
            ->implode(', ');
    }
}
