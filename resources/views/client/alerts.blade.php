@extends('layouts.app')
@section('title', 'Alertas')

@section('content')
<div class="page-header flex items-center justify-between" style="flex-wrap:wrap;gap:12px">
  <div>
    <h1 class="page-title">Central de Alertas</h1>
    <p class="page-sub">Cada alerta requer sua atenção. Aja rápido para proteger seus acessos.</p>
  </div>
  @if($alerts->total() > 0)
    <form method="POST" action="{{ route('client.alerts.readAll') }}">
      @csrf
      <button type="submit" class="btn btn-outline btn-sm">Marcar todos como lidos</button>
    </form>
  @endif
</div>

@if($alerts->isEmpty())
  <div class="card">
    <div class="empty-state">
      <div class="empty-state-icon">🛡</div>
      <div class="empty-state-title">Nenhum alerta</div>
      <div class="empty-state-sub">Seus dados estão protegidos. SecRadar monitorando ativamente.</div>
    </div>
  </div>
@else
  @foreach($alerts as $alert)
    @php $result = $alert->scanResult; $unread = !$alert->read; @endphp
    <div class="card mb-4" style="
      border-left:3px solid var(--{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warn' : 'accent2') }});
      opacity:{{ $unread ? '1' : '.7' }};
      transition:opacity .2s;
    ">
      <div class="flex items-center justify-between" style="flex-wrap:wrap;gap:12px">
        <div class="flex items-center gap-3">
          <div class="alert-icon {{ $alert->severity }}" style="flex-shrink:0">
            @if(in_array($alert->severity,['critical','high']))⚠@else○@endif
          </div>
          <div>
            <div style="font-weight:600;font-size:14px;display:flex;align-items:center;gap:8px">
              {{ $result?->breach_name ?? 'Incidente de Segurança Detectado' }}
              @if($unread)<span class="pill pill-critical" style="font-size:9px">NOVO</span>@endif
            </div>
            <div class="text-muted" style="font-size:12px;margin-top:3px">
              E-mail: {{ $result?->monitoredEmail?->email ?? auth()->user()->email }}
              · {{ $alert->created_at->format('d/m/Y \à\s H:i') }}
            </div>
          </div>
        </div>
        <span class="pill pill-{{ $alert->severity }}">{{ $result?->severityLabel() }}</span>
      </div>

      @if($result)
        <div class="divider"></div>
        <div class="grid-2" style="gap:12px">
          <div>
            <div class="text-muted" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px">Dados possivelmente expostos</div>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @foreach($result->data_exposed as $d)
                <span class="pill pill-medium">{{ $d }}</span>
              @endforeach
            </div>
          </div>
          <div>
            <div class="text-muted" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px">O que fazer</div>
            <div style="font-size:12px;color:var(--text);line-height:1.6">
              @if($result->severity === 'critical')
                Troque sua senha <strong>imediatamente</strong>. Ative autenticação em dois fatores. Monitore movimentações financeiras.
              @elseif($result->severity === 'high')
                Altere sua senha nos próximos dias. Fique atento a e-mails suspeitos.
              @else
                Monitore seus acessos. Considere trocar a senha como precaução.
              @endif
            </div>
          </div>
        </div>
      @endif

      @if($unread)
        <div style="margin-top:12px">
          <form method="POST" action="{{ route('client.alerts.read', $alert->id) }}" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">Marcar como lido</button>
          </form>
        </div>
      @endif
    </div>
  @endforeach

  <div>{{ $alerts->links() }}</div>
@endif
@endsection
