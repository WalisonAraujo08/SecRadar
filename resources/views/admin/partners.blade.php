<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parceiros — Admin SecRadar</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@400;500&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#02191b;--bg2:#031f22;--surface:#073035;--border:rgba(0,212,170,.12);--accent:#00d4aa;--text:#e8f4f2;--muted:#7aada6;--danger:#ff4757;--safe:#2ed573;--warn:#ffa502}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh}
.nav{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between}
.brand{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:2px;color:var(--warn)}
.content{padding:32px 24px;max-width:1100px;margin:0 auto}
.page-title{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;color:#fff;margin-bottom:24px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:24px}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);font-family:'Rajdhani',sans-serif;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:var(--accent);display:flex;align-items:center;justify-content:space-between}
table{width:100%;border-collapse:collapse;font-size:13px}
th{background:#052428;color:var(--muted);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;padding:10px 16px;text-align:left;font-weight:500}
td{padding:12px 16px;border-bottom:1px solid rgba(0,212,170,.05);vertical-align:middle}
tr:last-child td{border-bottom:none}
.pill{display:inline-flex;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.pill-warn{background:rgba(255,165,2,.2);color:var(--warn)}
.pill-safe{background:rgba(46,213,115,.2);color:var(--safe)}
.btn{display:inline-flex;align-items:center;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;border:none;font-family:inherit}
.btn-approve{background:rgba(46,213,115,.15);color:var(--safe);border:1px solid rgba(46,213,115,.3)}
.btn-reject{background:rgba(255,71,87,.15);color:var(--danger);border:1px solid rgba(255,71,87,.3)}
.flash{padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;background:rgba(46,213,115,.1);border:1px solid rgba(46,213,115,.2);color:var(--safe)}
a{color:var(--accent);text-decoration:none}
.empty{padding:32px;text-align:center;color:var(--muted);font-size:13px}
</style>
</head>
<body>
<nav class="nav">
  <div class="brand">⚙ SECRADAR ADMIN</div>
  <div style="display:flex;gap:16px;font-size:13px">
    <a href="{{ route('admin.dashboard') }}" style="color:var(--muted)">Dashboard</a>
    <a href="{{ route('admin.clients.index') }}" style="color:var(--muted)">Clientes</a>
    <a href="{{ route('admin.partners.index') }}" style="color:var(--accent)">Parceiros</a>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:13px;font-family:inherit">Sair</button>
    </form>
  </div>
</nav>

<div class="content">
  <h1 class="page-title">Gerenciar Parceiros</h1>

  @if(session('success'))
    <div class="flash">{{ session('success') }}</div>
  @endif

  {{-- PENDENTES --}}
  <div class="card">
    <div class="card-header">
      Solicitações pendentes
      @if($pending->count() > 0)
        <span class="pill pill-warn">{{ $pending->count() }} aguardando</span>
      @endif
    </div>
    @if($pending->isEmpty())
      <div class="empty">Nenhuma solicitação pendente.</div>
    @else
      <table>
        <thead>
          <tr><th>Nome</th><th>E-mail</th><th>Plano</th><th>Solicitou em</th><th>Ações</th></tr>
        </thead>
        <tbody>
          @foreach($pending as $u)
            <tr>
              <td style="font-weight:500">{{ $u->name }}</td>
              <td style="color:var(--muted)">{{ $u->email }}</td>
              <td>
                @if($u->subscription?->isActive())
                  <span class="pill pill-safe">{{ $u->subscription->isCorporate() ? 'Corporativo' : 'Pessoal' }}</span>
                @else
                  <span style="color:var(--muted)">Sem plano</span>
                @endif
              </td>
              <td style="color:var(--muted)">{{ $u->partner_requested_at?->format('d/m/Y H:i') }}</td>
              <td style="display:flex;gap:8px">
                <form method="POST" action="{{ route('admin.partners.approve', $u->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-approve">✔ Aprovar</button>
                </form>
                <form method="POST" action="{{ route('admin.partners.reject', $u->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-reject">✖ Recusar</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- APROVADOS --}}
  <div class="card">
    <div class="card-header">
      Parceiros ativos
      <span class="pill pill-safe">{{ $approved->count() }} parceiros</span>
    </div>
    @if($approved->isEmpty())
      <div class="empty">Nenhum parceiro aprovado ainda.</div>
    @else
      <table>
        <thead>
          <tr><th>Nome</th><th>E-mail</th><th>Código</th><th>Indicados</th><th>PIX</th></tr>
        </thead>
        <tbody>
          @foreach($approved as $u)
            <tr>
              <td style="font-weight:500">{{ $u->name }}</td>
              <td style="color:var(--muted)">{{ $u->email }}</td>
              <td><code style="background:var(--bg3);padding:2px 8px;border-radius:4px;font-size:12px;color:var(--accent)">{{ $u->referral_code }}</code></td>
              <td>{{ $u->referrals_count ?? 0 }}</td>
              <td style="color:var(--muted)">{{ $u->pix_key ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
</body>
</html>
