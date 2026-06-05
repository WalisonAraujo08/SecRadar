<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $client->name }} — Admin SecRadar</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@400;500&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#02191b;--bg2:#031f22;--surface:#073035;--border:rgba(0,212,170,.12);--accent:#00d4aa;--text:#e8f4f2;--muted:#7aada6;--danger:#ff4757;--safe:#2ed573;--warn:#ffa502}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh}
.nav{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between}
.content{padding:32px 24px;max-width:1100px;margin:0 auto}
.page-title{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;color:#fff;margin-bottom:4px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:16px}
.card-header{padding:14px 20px;border-bottom:1px solid var(--border);font-family:'Rajdhani',sans-serif;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:var(--accent)}
.card-body{padding:16px 20px}
.info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(0,212,170,.06);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-label{color:var(--muted)}
.info-val{color:var(--text);font-weight:500}
table{width:100%;border-collapse:collapse;font-size:13px}
th{background:#052428;color:var(--muted);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;padding:10px 16px;text-align:left;font-weight:500}
td{padding:10px 16px;border-bottom:1px solid rgba(0,212,170,.05)}
.pill{display:inline-flex;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.pill-critical{background:rgba(255,71,87,.2);color:#ff6b78}
.pill-high{background:rgba(255,100,30,.2);color:#ff8040}
.pill-medium{background:rgba(255,165,2,.2);color:#ffbb33}
.pill-low{background:rgba(0,168,255,.15);color:var(--accent2,#00a8ff)}
.pill-safe{background:rgba(46,213,115,.2);color:var(--safe)}
a{color:var(--accent);text-decoration:none}
.back{display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;margin-bottom:20px}
.back:hover{color:var(--text)}
</style>
</head>
<body>
<nav class="nav">
  <div>
    <img src="/images/logo_secradar.png" alt="Azuron" style="height:32px;width:auto;object-fit:contain">
  </div>
  <div style="display:flex;gap:16px;font-size:13px">
    <a href="{{ route('admin.dashboard') }}" style="color:var(--muted)">Dashboard</a>
    <a href="{{ route('admin.clients.index') }}" style="color:var(--accent)">Clientes</a>
    <a href="{{ route('admin.partners.index') }}" style="color:var(--muted)">Parceiros</a>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:13px;font-family:inherit">Sair</button>
    </form>
  </div>
</nav>

<div class="content">
  <a href="{{ route('admin.clients.index') }}" class="back">← Voltar para clientes</a>

  <h1 class="page-title">{{ $client->name }}</h1>
  <p style="color:var(--muted);font-size:13px;margin-bottom:24px">{{ $client->email }}</p>

  <div class="grid">
    {{-- Info pessoal --}}
    <div class="card">
      <div class="card-header">Dados do cliente</div>
      <div class="card-body">
        <div class="info-row"><span class="info-label">Nome</span><span class="info-val">{{ $client->name }}</span></div>
        <div class="info-row"><span class="info-label">E-mail</span><span class="info-val">{{ $client->email }}</span></div>
        <div class="info-row"><span class="info-label">CPF</span><span class="info-val">{{ $client->cpf ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Telefone</span><span class="info-val">{{ $client->phone ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Cadastro</span><span class="info-val">{{ $client->created_at->format('d/m/Y H:i') }}</span></div>
        <div class="info-row">
          <span class="info-label">Status</span>
          <span class="info-val" style="color:{{ $client->is_active ? 'var(--safe)' : 'var(--danger)' }}">
            {{ $client->is_active ? 'Ativo' : 'Inativo' }}
          </span>
        </div>
      </div>
    </div>

    {{-- Assinatura --}}
    <div class="card">
      <div class="card-header">Assinatura</div>
      <div class="card-body">
        @if($client->subscription)
          <div class="info-row"><span class="info-label">Plano</span><span class="info-val">{{ $client->subscription->isCorporate() ? 'Corporativo' : 'Pessoal' }}</span></div>
          <div class="info-row"><span class="info-label">Status</span><span class="info-val" style="color:{{ $client->subscription->isActive() ? 'var(--safe)' : 'var(--warn)' }}">{{ $client->subscription->status }}</span></div>
          <div class="info-row"><span class="info-label">Valor</span><span class="info-val">R$ {{ number_format($client->subscription->monthlyAmount(), 2, ',', '.') }}/mês</span></div>
          <div class="info-row"><span class="info-label">E-mails extras</span><span class="info-val">{{ $client->subscription->extra_emails }}</span></div>
          <div class="info-row"><span class="info-label">Próx. cobrança</span><span class="info-val">{{ $client->subscription->next_billing_date?->format('d/m/Y') ?? '—' }}</span></div>
        @else
          <p style="color:var(--muted);font-size:13px">Sem assinatura ativa.</p>
        @endif
      </div>
    </div>
  </div>

  {{-- E-mails monitorados --}}
  <div class="card">
    <div class="card-header">E-mails monitorados</div>
    <table>
      <thead><tr><th>E-mail</th><th>Tipo</th><th>Última varredura</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($client->monitoredEmails as $em)
          <tr>
            <td>{{ $em->email }}</td>
            <td>{{ $em->is_primary ? 'Principal' : 'Adicional' }}</td>
            <td style="color:var(--muted)">{{ $em->last_scanned_at?->format('d/m/Y H:i') ?? 'Nunca' }}</td>
            <td><span class="pill pill-safe">{{ $em->status }}</span></td>
          </tr>
        @empty
          <tr><td colspan="4" style="color:var(--muted);text-align:center;padding:20px">Nenhum e-mail cadastrado</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Alertas recentes --}}
  <div class="card">
    <div class="card-header">Alertas recentes</div>
    <table>
      <thead><tr><th>Incidente</th><th>Severidade</th><th>Dados expostos</th><th>Detectado</th><th>Lido</th></tr></thead>
      <tbody>
        @forelse($client->alerts->take(20) as $alert)
          <tr>
            <td>{{ $alert->scanResult?->breach_name ?? '—' }}</td>
            <td><span class="pill pill-{{ $alert->severity }}">{{ $alert->scanResult?->severityLabel() ?? $alert->severity }}</span></td>
            <td style="color:var(--muted)">{{ $alert->scanResult?->dataExposedLabel() ?? '—' }}</td>
            <td style="color:var(--muted)">{{ $alert->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $alert->read ? '✔' : '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:var(--muted);text-align:center;padding:20px">Nenhum alerta</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
