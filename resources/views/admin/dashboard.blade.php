<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — SecRadar Azuron</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@400;500&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#02191b;--bg2:#031f22;--surface:#073035;--border:rgba(0,212,170,.12);--accent:#00d4aa;--text:#e8f4f2;--muted:#7aada6;--danger:#ff4757;--safe:#2ed573;--warn:#ffa502}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh}
.nav{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between}
.brand{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:2px;color:var(--warn)}
.content{padding:32px 24px;max-width:1200px;margin:0 auto}
.page-title{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;color:#fff;margin-bottom:24px}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:32px}
.stat{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px}
.stat-label{font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
.stat-val{font-family:'Rajdhani',sans-serif;font-size:36px;font-weight:700}
.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:24px}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);font-family:'Rajdhani',sans-serif;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:var(--accent)}
table{width:100%;border-collapse:collapse;font-size:13px}
th{background:#052428;color:var(--muted);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;padding:10px 16px;text-align:left;font-weight:500}
td{padding:12px 16px;border-bottom:1px solid rgba(0,212,170,.05);vertical-align:middle}
tr:last-child td{border-bottom:none}
.pill{display:inline-flex;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.pill-active{background:rgba(46,213,115,.2);color:var(--safe)}
.pill-inactive{background:rgba(255,71,87,.15);color:var(--danger)}
.pill-pending{background:rgba(255,165,2,.15);color:var(--warn)}
a{color:var(--accent);text-decoration:none}
</style>
</head>
<body>
<nav class="nav">
  <div class="brand">
  <img src="/images/logo_secradar.png" alt="Azuron" style="height:120px;width:auto;object-fit:contain">
</div>
  <div style="display:flex;gap:16px;font-size:13px">
    <a href="{{ route('admin.clients.index') }}" style="color:var(--muted)">Clientes</a>
    <a href="{{ route('admin.logs.index') }}" style="color:var(--muted)">Logs</a>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:13px;font-family:inherit">Sair</button>
    </form>
  </div>
</nav>

<div class="content">
  <h1 class="page-title">Dashboard Administrativo</h1>

  <div class="stats">
    <div class="stat">
      <div class="stat-label">Total de clientes</div>
      <div class="stat-val" style="color:var(--accent)">{{ $stats['total_clients'] }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Assinaturas ativas</div>
      <div class="stat-val" style="color:var(--safe)">{{ $stats['active_subs'] }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">MRR</div>
      <div class="stat-val" style="color:var(--accent);font-size:24px;padding-top:8px">R$ {{ number_format($stats['mrr'], 2, ',', '.') }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Vazamentos detectados</div>
      <div class="stat-val" style="color:var(--danger)">{{ $stats['total_breaches'] }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Alertas hoje</div>
      <div class="stat-val" style="color:var(--warn)">{{ $stats['alerts_today'] }}</div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Últimos clientes cadastrados</div>
    <table>
      <thead><tr><th>Nome</th><th>E-mail</th><th>Assinatura</th><th>Cadastro</th></tr></thead>
      <tbody>
        @foreach($recentClients as $c)
          <tr>
            <td><a href="{{ route('admin.clients.show', $c->id) }}">{{ $c->name }}</a></td>
            <td style="color:var(--muted)">{{ $c->email }}</td>
            <td>
              @if($c->subscription?->isActive())
                <span class="pill pill-active">Ativo</span>
              @elseif($c->subscription)
                <span class="pill pill-pending">{{ $c->subscription->status }}</span>
              @else
                <span class="pill pill-inactive">Sem assinatura</span>
              @endif
            </td>
            <td style="color:var(--muted)">{{ $c->created_at->format('d/m/Y') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
