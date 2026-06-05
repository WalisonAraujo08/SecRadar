@extends('layouts.app')
@section('title', 'Seja um Parceiro')

@section('content')
<div class="page-header">
  <h1 class="page-title">Programa de Parceiros</h1>
  <p class="page-sub">Ganhe 30% de comissão indicando o SecRadar.</p>
</div>

@if(session('rejected'))
  <div class="flash flash-error">Sua solicitação anterior foi recusada. Você pode solicitar novamente.</div>
@endif

<div style="max-width:600px;margin:0 auto">
  <div class="card" style="border-color:rgba(255,165,2,.3);text-align:center;padding:40px">
    <div style="font-size:56px;margin-bottom:16px">💰</div>
    <div style="font-family:var(--font-brand);font-size:28px;font-weight:700;color:var(--warn);margin-bottom:8px">Ganhe 30% de comissão</div>
    <p class="text-muted" style="font-size:14px;line-height:1.7;margin-bottom:32px">
      Indique o SecRadar para amigos, familiares ou clientes e ganhe <strong style="color:#fff">30% do valor</strong> pago por cada indicado todo mês, enquanto ele permanecer ativo. Receba via PIX automaticamente.
    </p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:32px;text-align:left">
      @foreach([
        ['💰','Plano Pessoal','R$ 8,97/mês por indicado'],
        ['🏢','Plano Corporativo','R$ 44,97/mês por indicado'],
        ['📲','Pagamento via PIX','Todo dia 10 do mês'],
        ['♾','Sem limite','Indique quantos quiser'],
      ] as [$icon,$title,$sub])
        <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:14px">
          <div style="font-size:20px;margin-bottom:6px">{{ $icon }}</div>
          <div style="font-size:13px;font-weight:500;color:var(--text)">{{ $title }}</div>
          <div style="font-size:12px;color:var(--muted)">{{ $sub }}</div>
        </div>
      @endforeach
    </div>

    <form method="POST" action="{{ route('client.referral.request') }}">
      @csrf
      <button type="submit" class="btn btn-full" style="background:var(--warn);color:var(--bg);font-size:16px;padding:14px;font-family:var(--font-brand);font-weight:700;letter-spacing:1px">
        Quero ser parceiro SecRadar
      </button>
    </form>
    <p class="text-muted" style="font-size:11px;margin-top:12px">Sua solicitação será analisada em até 24 horas.</p>
  </div>
</div>
@endsection
