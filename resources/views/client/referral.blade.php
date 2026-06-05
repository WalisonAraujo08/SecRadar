@extends('layouts.app')
@section('title', 'Programa de Parceiros')

@section('content')
<div class="page-header">
  <h1 class="page-title">Programa de Parceiros</h1>
  <p class="page-sub">Indique o SecRadar e ganhe comissões crescentes todo mês.</p>
</div>

{{-- NÍVEL ATUAL --}}
<div class="card mb-6" style="border-color:rgba(255,165,2,.3);background:rgba(255,165,2,.04)">
  <div class="flex items-center justify-between" style="flex-wrap:wrap;gap:16px">
    <div class="flex items-center gap-3">
      <div style="font-size:40px">{{ $currentLevel['icon'] }}</div>
      <div>
        <div style="font-family:var(--font-brand);font-size:20px;font-weight:700;color:var(--warn)">
          Parceiro {{ $currentLevel['name'] }}
        </div>
        <div class="text-muted" style="font-size:13px">
          {{ $currentLevel['rate'] }}% de comissão · {{ $activeCount }} indicado(s) ativo(s)
        </div>
      </div>
    </div>
    @if($nextLevel)
      <div style="text-align:right">
        <div style="font-size:12px;color:var(--muted);margin-bottom:6px">
          Faltam <strong style="color:#fff">{{ $nextLevel['min'] - $activeCount }}</strong> indicados para {{ $nextLevel['icon'] }} {{ $nextLevel['name'] }} ({{ $nextLevel['rate'] }}%)
        </div>
        <div style="width:200px;height:6px;background:var(--bg3);border-radius:3px;overflow:hidden">
          <div style="width:{{ $progressPct }}%;height:100%;background:linear-gradient(90deg,var(--warn),#ffcc02);border-radius:3px;transition:width .5s"></div>
        </div>
      </div>
    @else
      <span class="pill" style="background:rgba(255,165,2,.2);color:var(--warn);font-size:12px">Nível máximo atingido! 🎉</span>
    @endif
  </div>
</div>

{{-- TABELA DE NÍVEIS --}}
<div class="card mb-6">
  <div class="card-title">Tabela de níveis</div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Nível</th><th>Indicados ativos</th><th>Comissão</th><th>Ganho por Pessoal</th><th>Ganho por Corporativo</th></tr>
      </thead>
      <tbody>
        @foreach(\App\Services\CommissionService::LEVELS as $lvl)
          <tr style="{{ $currentLevel['name'] === $lvl['name'] ? 'background:rgba(255,165,2,.06)' : '' }}">
            <td>
              <span style="font-size:16px">{{ $lvl['icon'] }}</span>
              <strong style="color:{{ $currentLevel['name'] === $lvl['name'] ? 'var(--warn)' : 'var(--text)' }}">
                {{ $lvl['name'] }}
              </strong>
              @if($currentLevel['name'] === $lvl['name'])
                <span class="pill" style="background:rgba(255,165,2,.2);color:var(--warn);font-size:9px;margin-left:6px">ATUAL</span>
              @endif
            </td>
            <td class="text-muted">
              {{ $lvl['min'] }}{{ $lvl['max'] ? ' a ' . $lvl['max'] : '+' }} indicados
            </td>
            <td style="font-weight:600;color:var(--accent)">{{ $lvl['rate'] }}%</td>
            <td style="color:var(--safe)">R$ {{ number_format(29.90 * $lvl['rate'] / 100, 2, ',', '.') }}/mês</td>
            <td style="color:var(--safe)">R$ {{ number_format(149.90 * $lvl['rate'] / 100, 2, ',', '.') }}/mês</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- STATS --}}
<div class="stats-grid mb-6">
  <div class="stat-card">
    <div class="stat-label">Total ganho</div>
    <div class="stat-value safe">R$ {{ number_format($totalEarned, 2, ',', '.') }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Aguardando PIX</div>
    <div class="stat-value warn">R$ {{ number_format($pendingPayment, 2, ',', '.') }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Indicados ativos</div>
    <div class="stat-value info">{{ $activeCount }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Comissão atual</div>
    <div class="stat-value warn">{{ $currentLevel['rate'] }}%</div>
  </div>
</div>

<div class="grid-2">
  {{-- LINK --}}
  <div class="card">
    <div class="card-title">Seu link de indicação</div>
    <p class="text-muted" style="font-size:13px;margin-bottom:12px">Compartilhe — quem assinar pelo seu link gera comissão para você.</p>
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
      <span style="font-family:monospace;font-size:12px;color:var(--accent);word-break:break-all" id="ref-link">
        {{ url('/cadastro?ref=' . $user->referral_code) }}
      </span>
      <button onclick="copyLink()" class="btn btn-outline btn-sm" style="flex-shrink:0" id="copy-btn">Copiar</button>
    </div>
    <div style="background:rgba(255,165,2,.08);border:1px solid rgba(255,165,2,.2);border-radius:8px;padding:12px 14px">
      <div style="font-size:12px;color:var(--warn);font-weight:500;margin-bottom:6px">Comissão atual ({{ $currentLevel['rate'] }}%):</div>
      <div style="font-size:12px;color:var(--muted);line-height:1.8">
        • Plano Pessoal (R$ 29,90) → <strong style="color:#fff">R$ {{ number_format(29.90 * $currentLevel['rate'] / 100, 2, ',', '.') }}/mês</strong><br>
        • Plano Corporativo (R$ 149,90) → <strong style="color:#fff">R$ {{ number_format(149.90 * $currentLevel['rate'] / 100, 2, ',', '.') }}/mês</strong><br>
        • Pagamento via PIX todo dia 10 do mês
      </div>
    </div>
  </div>

  {{-- PIX --}}
  <div class="card">
    <div class="card-title">Chave PIX para receber</div>
    <p class="text-muted" style="font-size:13px;margin-bottom:16px">Cadastre sua chave PIX para receber suas comissões automaticamente.</p>
    <form method="POST" action="{{ route('client.referral.pix') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Chave PIX</label>
        <input type="text" name="pix_key" class="form-input" placeholder="CPF, e-mail, telefone ou chave aleatória" value="{{ $user->pix_key ?? '' }}">
      </div>
      <button type="submit" class="btn btn-primary btn-full">Salvar chave PIX</button>
    </form>
  </div>
</div>

{{-- INDICADOS --}}
<div class="card mt-6">
  <div class="card-title">Meus indicados</div>
  @if($referrals->isEmpty())
    <div class="empty-state">
      <div class="empty-state-icon">👥</div>
      <div class="empty-state-title">Nenhum indicado ainda</div>
      <div class="empty-state-sub">Compartilhe seu link e comece a ganhar!</div>
    </div>
  @else
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Nome</th><th>E-mail</th><th>Status</th><th>Total gerado</th><th>Desde</th></tr>
        </thead>
        <tbody>
          @foreach($referrals as $r)
            <tr>
              <td>{{ $r->name }}</td>
              <td class="text-muted">{{ $r->email }}</td>
              <td><span class="pill {{ $r->status === 'active' ? 'pill-safe' : 'pill-critical' }}">{{ $r->status === 'active' ? 'Ativo' : 'Cancelado' }}</span></td>
              <td style="color:var(--safe)">R$ {{ number_format($r->total_earned, 2, ',', '.') }}</td>
              <td class="text-muted">{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

@push('scripts')
<script>
function copyLink() {
  navigator.clipboard.writeText(document.getElementById('ref-link').textContent.trim()).then(() => {
    const btn = document.getElementById('copy-btn');
    btn.textContent = '✔ Copiado!';
    setTimeout(() => btn.textContent = 'Copiar', 2000);
  });
}
</script>
@endpush
@endsection
