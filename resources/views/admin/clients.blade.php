<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clientes — Admin SecRadar</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@400;500&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#02191b;--bg2:#031f22;--surface:#073035;--border:rgba(0,212,170,.12);--accent:#00d4aa;--text:#e8f4f2;--muted:#7aada6;--danger:#ff4757;--safe:#2ed573;--warn:#ffa502}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh}
.nav{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between}
.content{padding:32px 24px;max-width:1200px;margin:0 auto}
.page-title{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;color:#fff;margin-bottom:24px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:24px}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);font-family:'Rajdhani',sans-serif;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:var(--accent);display:flex;align-items:center;justify-content:space-between}
table{width:100%;border-collapse:collapse;font-size:13px}
th{background:#052428;color:var(--muted);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;padding:10px 16px;text-align:left;font-weight:500}
td{padding:12px 16px;border-bottom:1px solid rgba(0,212,170,.05);vertical-align:middle}
tr:last-child td{border-bottom:none}
.pill{display:inline-flex;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.pill-active{background:rgba(46,213,115,.2);color:var(--safe)}
.pill-inactive{background:rgba(255,71,87,.15);color:var(--danger)}
.pill-pending{background:rgba(255,165,2,.15);color:var(--warn)}
.btn{display:inline-flex;align-items:center;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;border:none;font-family:inherit;text-decoration:none}
.btn-outline{background:transparent;color:var(--accent);border:1px solid rgba(0,212,170,.3)}
a{color:var(--accent);text-decoration:none}
.flash{padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;background:rgba(46,213,115,.1);border:1px solid rgba(46,213,115,.2);color:var(--safe)}
</style>
</head>
<body>
<nav class="nav">
  <div>
    <img src="/images/logo_secradar.png" alt="Azuron" style="height:180px;width:auto;object-fit:contain">
  </div>
  <div style="display:flex;gap:16px;font-size:13px">
    <a href="{{ route('admin.dashboard') }}" style="color:var(--muted)">Dashboard</a>
    <a href="{{ route('admin.clients.index') }}" style="color:var(--accent)">Clientes</a>
    <a href="{{ route('admin.partners.index') }}" style="color:var(--muted)">Parceiros</a>
    <a href="{{ route('admin.logs.index') }}" style="color:var(--muted)">Logs</a>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:13px;font-family:inherit">Sair</button>
    </form>
  </div>
</nav>

<div class="content">
  <h1 class="page-title">Clientes</h1>

  @if(session('success'))
    <div class="flash">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-header">
      Todos os clientes
      <span class="pill pill-active">{{ $clients->total() }} total</span>
    </div>
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Plano</th>
          <th>E-mails monitorados</th>
          <th>Vazamentos</th>
          <th>Alertas</th>
          <th>Cadastro</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($clients as $c)
          <tr>
            <td style="font-weight:500">
              <a href="{{ route('admin.clients.show', $c->id) }}">{{ $c->name }}</a>
            </td>
            <td style="color:var(--muted)">{{ $c->email }}</td>
            <td>
              @if($c->subscription?->isActive())
                <span class="pill pill-active">
                  {{ $c->subscription->isCorporate() ? 'Corporativo' : 'Pessoal' }}
                </span>
              @elseif($c->subscription)
                <span class="pill pill-pending">{{ $c->subscription->status }}</span>
              @else
                <span class="pill pill-inactive">Sem plano</span>
              @endif
            </td>
            <td>{{ $c->monitored_emails_count ?? $c->monitoredEmails->count() }}</td>
            <td style="color:{{ $c->scan_results_count > 0 ? 'var(--danger)' : 'var(--safe)' }}">
              {{ $c->scan_results_count }}
            </td>
            <td style="color:{{ $c->alerts_count > 0 ? 'var(--warn)' : 'var(--muted)' }}">
              {{ $c->alerts_count }}
            </td>
            <td style="color:var(--muted)">{{ $c->created_at->format('d/m/Y') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.clients.toggle', $c->id) }}" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-outline" style="{{ !$c->is_active ? 'color:var(--safe)' : 'color:var(--danger)' }}">
                  {{ $c->is_active ? 'Desativar' : 'Ativar' }}
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div style="padding:16px">{{ $clients->links() }}</div>
  </div>
</div>
</body>
</html>
