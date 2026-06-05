@extends('layouts.app')
@section('title', 'E-mails Monitorados')

@section('content')
<div class="page-header">
  <h1 class="page-title">E-mails Monitorados</h1>
  <p class="page-sub">Cada e-mail adicionado é monitorado continuamente em tempo real.</p>
</div>

<div class="grid-2">

  {{-- CURRENT EMAILS --}}
  <div class="card">
    <div class="card-title">E-mails ativos</div>

    @foreach($emails as $em)
      <div class="flex items-center" style="padding:12px 0;border-bottom:1px solid rgba(0,212,170,.06);gap:12px">
        <div style="width:36px;height:36px;border-radius:8px;background:rgba(0,212,170,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $em->email }}</div>
          <div class="text-muted" style="font-size:11px">
            @if($em->is_primary)
              E-mail principal — incluído no plano
            @else
              E-mail adicional — + R$ 8,90/mês
            @endif
            @if($em->last_scanned_at)
              · Varredura {{ $em->last_scanned_at->diffForHumans() }}
            @endif
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          @if($em->is_primary)
            <span class="pill pill-info">principal</span>
          @else
            <form method="POST" action="{{ route('client.emails.destroy', $em->id) }}" onsubmit="return confirm('Remover este e-mail do monitoramento?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Remover</button>
            </form>
          @endif
          <span class="chip"><span class="chip-dot" style="background:var(--safe)"></span>Ativo</span>
        </div>
      </div>
    @endforeach

    @if($emails->isEmpty())
      <div class="empty-state" style="padding:24px 0">
        <div class="empty-state-icon">📧</div>
        <div class="empty-state-title">Nenhum e-mail</div>
      </div>
    @endif
  </div>

  {{-- ADD EMAIL --}}
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-title">Adicionar e-mail</div>
      <p class="text-muted" style="font-size:13px;margin-bottom:16px;line-height:1.6">
        Proteja também o e-mail do seu cônjuge, filhos ou e-mail corporativo.
        Cada e-mail adicional custa <strong style="color:var(--accent)">R$ 8,90/mês</strong> e é cobrado automaticamente na sua assinatura.
      </p>
      <form method="POST" action="{{ route('client.emails.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">E-mail para monitorar</label>
          <input type="email" name="email" class="form-input" placeholder="outro@email.com" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full">
          + Adicionar e-mail — R$ 8,90/mês
        </button>
      </form>
    </div>

    {{-- Marketing --}}
    <div class="card" style="background:rgba(0,168,255,.05);border-color:rgba(0,168,255,.2)">
      <div style="font-family:var(--font-brand);font-size:15px;color:var(--accent2);margin-bottom:8px">
        💡 Proteja toda sua família
      </div>
      <p class="text-muted" style="font-size:13px;line-height:1.6">
        Em 2025, mais de <strong style="color:var(--white)">16 bilhões de dados</strong> foram expostos em vazamentos. Seus filhos e cônjuge também estão vulneráveis — adicione os e-mails deles agora.
      </p>
    </div>
  </div>
</div>
@endsection
