@extends('layouts.app')
@section('title', 'Minha Assinatura')

@section('content')
<div class="page-header">
  <h1 class="page-title">Minha Assinatura</h1>
  <p class="page-sub">Gerencie seu plano SecRadar Azuron.</p>
</div>

@if($user->hasActiveSubscription())
  {{-- ACTIVE SUBSCRIPTION --}}
  <div class="grid-2 mb-6">
    <div class="card" style="border-color:rgba(46,213,115,.3)">
      <div class="card-title">Plano atual</div>
      <div style="display:flex;align-items:flex-end;gap:4px;margin-bottom:8px">
        <span style="font-family:var(--font-brand);font-size:40px;font-weight:700;color:var(--accent)">
          R$ {{ number_format($user->subscription->monthlyAmount(), 2, ',', '.') }}
        </span>
        <span class="text-muted" style="padding-bottom:8px">/mês</span>
      </div>
      <div style="margin-bottom:12px">
        @if($user->subscription->isCorporate())
          <span class="pill pill-info">Plano Corporativo</span>
        @else
          <span class="pill pill-safe">Plano Pessoal</span>
        @endif
      </div>
      <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:16px">
        <div class="chip"><span class="chip-dot" style="background:var(--safe)"></span>
          {{ $user->subscription->isCorporate() ? 'Até 20 e-mails corporativos' : 'Plano base — R$ 29,90/mês' }}
        </div>
        @if(!$user->subscription->isCorporate() && $user->subscription->extra_emails > 0)
          <div class="chip"><span class="chip-dot" style="background:var(--accent2)"></span>{{ $user->subscription->extra_emails }} e-mail(s) extra — + R$ {{ number_format($user->subscription->extra_emails * 8.90, 2, ',', '.') }}/mês</div>
        @endif
      </div>
      <div class="flex items-center gap-2">
        <span class="pill pill-safe">● Ativo</span>
        @if($user->subscription->next_billing_date)
          <span class="text-muted" style="font-size:12px">Próxima cobrança: {{ $user->subscription->next_billing_date->format('d/m/Y') }}</span>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-title">O que está incluso</div>
      <div style="display:flex;flex-direction:column;gap:10px">
        @if($user->subscription->isCorporate())
          @foreach(['Até 20 e-mails monitorados','Alertas em tempo real','Varredura automática 24h','Painel web responsivo','Suporte prioritário'] as $f)
            <div class="flex items-center gap-2" style="font-size:13px">
              <span style="color:var(--safe)">✔</span>
              <span class="text-muted">{{ $f }}</span>
            </div>
          @endforeach
        @else
          @foreach(['1 e-mail principal monitorado','Alertas e-mail + WhatsApp','Varredura automática 24h','Painel web responsivo','Até 5 e-mails extras por R$ 8,90/cada'] as $f)
            <div class="flex items-center gap-2" style="font-size:13px">
              <span style="color:var(--safe)">✔</span>
              <span class="text-muted">{{ $f }}</span>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>

  {{-- Cancel --}}
  <div class="card" style="border-color:rgba(255,71,87,.2)">
    <div class="card-title" style="color:var(--danger)">Cancelar assinatura</div>
    <p class="text-muted" style="font-size:13px;margin-bottom:16px">
      Ao cancelar, você mantém o acesso até o fim do período já pago.
    </p>
    <form method="POST" action="{{ route('subscription.cancel') }}" onsubmit="return confirm('Tem certeza que deseja cancelar?')">
      @csrf
      <button type="submit" class="btn btn-danger btn-sm">Cancelar assinatura</button>
    </form>
  </div>

@else
  {{-- PLANOS --}}
  <div class="grid-3 mb-6">

    {{-- Plano Pessoal --}}
    <div class="card" style="border-color:rgba(0,212,170,.3);position:relative">
      <div style="font-family:var(--font-brand);font-size:12px;letter-spacing:2px;color:var(--accent);margin-bottom:12px">PESSOAL</div>
      <div style="font-family:var(--font-brand);font-size:48px;font-weight:700;color:#fff;line-height:1">
        R$ 29<span style="font-size:24px">,90</span>
      </div>
      <div class="text-muted" style="font-size:13px;margin-bottom:20px">por mês</div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:24px">
        @foreach(['1 e-mail monitorado','CPF + telefone','Alertas e-mail + WhatsApp','Varredura 24h','Até 5 e-mails extras'] as $f)
          <div class="flex items-center gap-2" style="font-size:13px">
            <span style="color:var(--safe)">✔</span>
            <span class="text-muted">{{ $f }}</span>
          </div>
        @endforeach
        <div class="flex items-center gap-2" style="font-size:13px">
          <span style="color:var(--accent2)">+</span>
          <span class="text-muted">E-mails extras por R$ 8,90/mês</span>
        </div>
      </div>
      <button onclick="selectPlan('personal', 29.90)" class="btn btn-primary btn-full">Assinar agora</button>
    </div>

    {{-- Plano Corporativo --}}
    <div class="card" style="border:2px solid rgba(0,168,255,.5);position:relative">
      <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--accent2);color:var(--bg);font-family:var(--font-brand);font-size:10px;font-weight:700;letter-spacing:2px;padding:4px 16px;border-radius:20px">MAIS POPULAR</div>
      <div style="font-family:var(--font-brand);font-size:12px;letter-spacing:2px;color:var(--accent2);margin-bottom:12px">CORPORATIVO</div>
      <div style="font-family:var(--font-brand);font-size:48px;font-weight:700;color:#fff;line-height:1">
        R$ 149<span style="font-size:24px">,90</span>
      </div>
      <div class="text-muted" style="font-size:13px;margin-bottom:20px">por mês — preço fixo</div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:24px">
        @foreach(['Até 20 e-mails corporativos','CPF + telefone de cada membro','Alertas e-mail + WhatsApp','Varredura automática 24h','Painel web responsivo','Suporte prioritário'] as $f)
          <div class="flex items-center gap-2" style="font-size:13px">
            <span style="color:var(--accent2)">✔</span>
            <span class="text-muted">{{ $f }}</span>
          </div>
        @endforeach
      </div>
      <button onclick="selectPlan('corporate', 149.90)" class="btn btn-full" style="background:var(--accent2);color:var(--bg)">Assinar agora</button>
    </div>

    {{-- Parceiro --}}
    <div class="card" style="border-color:rgba(255,165,2,.3)">
      <div style="font-family:var(--font-brand);font-size:12px;letter-spacing:2px;color:var(--warn);margin-bottom:12px">SEJA PARCEIRO</div>
      <div style="font-size:36px;margin-bottom:8px">💰</div>
      <div style="font-family:var(--font-brand);font-size:20px;font-weight:700;color:#fff;margin-bottom:8px">Ganhe 30% de comissão</div>
      <div class="text-muted" style="font-size:13px;margin-bottom:20px;line-height:1.6">
        Indique o SecRadar e ganhe 30% todo mês enquanto seu indicado permanecer ativo. Receba via PIX automaticamente.
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:24px">
        @foreach(['Link de indicação exclusivo','Painel de comissões','Pagamento mensal via PIX','Sem limite de indicações'] as $f)
          <div class="flex items-center gap-2" style="font-size:13px">
            <span style="color:var(--warn)">✔</span>
            <span class="text-muted">{{ $f }}</span>
          </div>
        @endforeach
      </div>
      <a href="{{ route('referral.index') }}" class="btn btn-full" style="background:rgba(255,165,2,.15);color:var(--warn);border:1px solid rgba(255,165,2,.3)">Quero ser parceiro</a>
    </div>
  </div>

  {{-- CHECKOUT --}}
  <div class="card" id="checkout-section" style="display:none">
    <div class="card-title" id="checkout-title">Assinar Plano Pessoal</div>
    <div id="mp-checkout-container"></div>
    <p class="text-center text-muted" style="font-size:11px;margin-top:16px">
      🔒 Pagamento seguro via Mercado Pago
    </p>
  </div>
@endif

@push('scripts')
@if(!$user->hasActiveSubscription())
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
const mp = new MercadoPago('{{ $mpPublicKey }}', { locale: 'pt-BR' });
let currentPlan = 'personal';
let currentAmount = 29.90;
let brickController = null;

async function selectPlan(plan, amount) {
  currentPlan = plan;
  currentAmount = amount;
  const section = document.getElementById('checkout-section');
  const title = document.getElementById('checkout-title');
  section.style.display = 'block';
  title.textContent = plan === 'corporate' ? 'Assinar Plano Corporativo — R$ 149,90/mês' : 'Assinar Plano Pessoal — R$ 29,90/mês';
  section.scrollIntoView({ behavior: 'smooth' });

  if (brickController) await brickController.unmount();

  brickController = await mp.bricks().create('cardPayment', 'mp-checkout-container', {
    initialization: {
      amount: amount,
      payer: { email: '{{ auth()->user()->email }}' },
    },
    customization: {
      visual: {
        style: {
          theme: 'dark',
          customVariables: {
            baseColor: '#00d4aa',
            textPrimaryColor: '#e8f4f2',
            inputBackgroundColor: '#052428',
            formBackgroundColor: '#073035',
          }
        }
      },
      paymentMethods: { creditCard: 'all', debitCard: 'all' }
    },
    callbacks: {
      onSubmit: async (cardFormData) => {
        const res = await fetch('{{ route("subscription.create") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            card_token: cardFormData.token,
            plan_type: currentPlan,
            amount: currentAmount,
          })
        });
        const data = await res.json();
        if (data.redirect) window.location.href = data.redirect;
      },
      onError: (error) => console.error(error),
    },
  });
}
</script>
@endif
@endpush
@endsection
