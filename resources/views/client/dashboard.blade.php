@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
  <h1 class="page-title">Olá, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
  <p class="page-sub">Seus dados estão sendo monitorados a todo momento pelo SecRadar.</p>
</div>

{{-- STATUS HERO --}}
@php $hasCritical = $stats['critical_alerts'] > 0; @endphp
<div class="card mb-6" style="border-color:{{ $hasCritical ? 'rgba(255,71,87,.4)' : 'rgba(46,213,115,.3)' }};background:{{ $hasCritical ? 'rgba(255,71,87,.06)' : 'rgba(46,213,115,.05)' }}">
  <div class="flex items-center justify-between" style="flex-wrap:wrap;gap:12px">
    <div class="flex items-center gap-3">
      <div style="width:48px;height:48px;border-radius:50%;background:{{ $hasCritical ? 'rgba(255,71,87,.2)' : 'rgba(46,213,115,.2)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
        @if($hasCritical)
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        @else
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--safe)" stroke-width="2"><rect x="5" y="11" width="14" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        @endif
      </div>
      <div>
        <div style="font-family:var(--font-brand);font-size:17px;font-weight:700;color:{{ $hasCritical ? 'var(--danger)' : 'var(--safe)' }}">
          {{ $hasCritical ? '⚠ Atenção necessária' : '✔ Você está protegido' }}
        </div>
        <div class="text-muted" style="font-size:13px">
          {{ $hasCritical
            ? "{$stats['critical_alerts']} alerta(s) crítico(s) requerem sua atenção"
            : 'Nenhum vazamento ativo detectado. SecRadar monitorando.' }}
        </div>
      </div>
    </div>
    @if($hasCritical)
      <a href="{{ route('client.alerts.index') }}" class="btn btn-danger">Ver alertas</a>
    @else
      <form method="POST" action="{{ route('client.scan.start') }}">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          Verificar agora
        </button>
      </form>
    @endif
  </div>
</div>

{{-- STATS --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">E-mails monitorados</div>
    <div class="stat-value info">{{ $stats['emails_monitored'] }}</div>
    <div class="stat-sub">em tempo real</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Vazamentos detectados</div>
    <div class="stat-value {{ $stats['total_breaches'] > 0 ? 'danger' : 'safe' }}">{{ $stats['total_breaches'] }}</div>
    <div class="stat-sub">total histórico</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Alertas não lidos</div>
    <div class="stat-value {{ $stats['critical_alerts'] > 0 ? 'danger' : 'safe' }}">{{ $stats['critical_alerts'] }}</div>
    <div class="stat-sub">aguardando revisão</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Última varredura</div>
    <div class="stat-value info" style="font-size:18px;padding-top:6px">
      {{ $stats['last_scan'] ? $stats['last_scan']->diffForHumans() : '—' }}
    </div>
    <div class="stat-sub">automática contínua</div>
  </div>
</div>

{{-- MAIN GRID --}}
<div class="grid-2">

  {{-- Alertas recentes --}}
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <div class="card-title" style="margin-bottom:0">Alertas recentes</div>
      <a href="{{ route('client.alerts.index') }}" class="text-muted" style="font-size:12px">Ver todos</a>
    </div>

    @forelse($recentAlerts as $alert)
      @php $result = $alert->scanResult; @endphp
      <div class="alert-item" style="{{ !$alert->read ? 'opacity:1' : 'opacity:.65' }}">
        <div class="alert-icon {{ $alert->severity }}">
          @if($alert->severity === 'critical') ⚠
          @elseif($alert->severity === 'high') ⚡
          @else ○
          @endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $result?->breach_name ?? 'Incidente detectado' }}
          </div>
          <div class="text-muted" style="font-size:11px;margin-top:2px">
            {{ $result?->monitoredEmail?->email ?? auth()->user()->email }} ·
            {{ $alert->created_at->diffForHumans() }}
          </div>
        </div>
        <span class="pill pill-{{ $alert->severity }}">{{ $result?->severityLabel() }}</span>
      </div>
    @empty
      <div class="empty-state" style="padding:32px 0">
        <div class="empty-state-icon">🛡</div>
        <div class="empty-state-title">Nenhum alerta</div>
        <div class="empty-state-sub">Seus dados estão limpos</div>
      </div>
    @endforelse
  </div>

  {{-- E-mails monitorados + marketing --}}
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <div class="card-title" style="margin-bottom:0">E-mails monitorados</div>
        <a href="{{ route('client.emails.index') }}" class="btn btn-outline btn-sm">+ Adicionar</a>
      </div>
      @foreach($user->monitoredEmails as $em)
        <div class="chip" style="margin-bottom:8px;width:100%">
          <span class="chip-dot" style="background:var(--safe)"></span>
          <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $em->email }}</span>
          @if($em->is_primary)<span class="pill pill-info" style="font-size:10px">principal</span>@endif
        </div>
      @endforeach
      @if($user->monitoredEmails->count() === 0)
        <p class="text-muted" style="font-size:13px">Nenhum e-mail configurado.</p>
      @endif
    </div>

    {{-- Marketing card --}}
    <div class="card" style="background:linear-gradient(135deg,rgba(0,212,170,.08),rgba(0,168,255,.05));border-color:rgba(0,212,170,.2)">
      <div style="font-family:var(--font-brand);font-size:16px;font-weight:700;color:var(--accent);margin-bottom:8px">
        🛡 SecRadar vigia 24h por dia
      </div>
      <p style="font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:12px">
        Enquanto você dorme, nossa tecnologia rastreia continuamente centenas de bases de dados comprometidas para manter seus dados seguros.
      </p>
      <div class="flex items-center gap-2" style="flex-wrap:wrap">
        <span class="chip"><span class="chip-dot" style="background:var(--safe)"></span>Monitoramento ativo</span>
        <span class="chip"><span class="chip-dot" style="background:var(--accent2)"></span>Alertas em tempo real</span>
      </div>
    </div>
  </div>
</div>
{{-- CARD PARCEIRO --}}
<div class="card mt-6" style="background:linear-gradient(135deg,rgba(255,165,2,.08),rgba(255,165,2,.03));border-color:rgba(255,165,2,.25)">
  <div class="flex items-center justify-between" style="flex-wrap:wrap;gap:16px">
    <div class="flex items-center gap-3">
      <div style="width:48px;height:48px;border-radius:50%;background:rgba(255,165,2,.15);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">💰</div>
      <div>
        <div style="font-family:var(--font-brand);font-size:16px;font-weight:700;color:var(--warn);margin-bottom:2px">
          Programa de Parceiros — Ganhe 30%
        </div>
        <p class="text-muted" style="font-size:13px">
          Indique o SecRadar e ganhe 30% de comissão todo mês para cada cliente ativo que você trouxer.
        </p>
      </div>
    </div>
    <a href="{{ route('client.referral.index') }}" class="btn" style="background:rgba(255,165,2,.15);color:var(--warn);border:1px solid rgba(255,165,2,.3);white-space:nowrap">
      Ver meu painel de parceiro →
    </a>
  </div>
</div>

@endsection
