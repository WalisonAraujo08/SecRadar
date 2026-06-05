<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#02191b">
<title>Entrar — SecRadar Azuron</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#02191b;--bg2:#031f22;--bg3:#052428;--surface:#073035;--border:rgba(0,212,170,.12);--accent:#00d4aa;--danger:#ff4757;--text:#e8f4f2;--muted:#7aada6;--muted2:#4a7a75}
html,body{min-height:100%;background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;-webkit-font-smoothing:antialiased}
body{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100dvh;padding:20px}
.auth-brand{text-align:center;margin-bottom:32px}
.auth-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:32px 28px;width:100%;max-width:420px}
@media(min-width:480px){.auth-card{padding:36px 40px}}
.auth-title{font-family:'Rajdhani',sans-serif;font-size:24px;font-weight:700;color:#fff;margin-bottom:4px}
.auth-sub{font-size:13px;color:var(--muted);margin-bottom:24px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-input{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:11px 14px;color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border .2s}
.form-input:focus{border-color:var(--accent)}
.form-input::placeholder{color:var(--muted2)}
.btn-primary{display:block;width:100%;padding:13px;background:var(--accent);color:var(--bg);border:none;border-radius:8px;font-size:15px;font-weight:600;font-family:'Rajdhani',sans-serif;letter-spacing:1px;cursor:pointer;transition:opacity .15s;text-transform:uppercase}
.btn-primary:hover{opacity:.85}
.auth-footer{text-align:center;margin-top:20px;font-size:13px;color:var(--muted)}
.auth-footer a{color:var(--accent)}
.flash-error{background:rgba(255,71,87,.12);border:1px solid rgba(255,71,87,.25);color:#ff6b78;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--muted2);font-size:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
.marketing-strip{text-align:center;margin-top:24px;font-size:12px;color:var(--muted2)}
.marketing-strip strong{color:var(--accent)}
.attempts-warning{background:rgba(255,165,2,.1);border:1px solid rgba(255,165,2,.25);color:var(--warn);padding:10px 14px;border-radius:8px;font-size:12px;margin-bottom:16px;display:none}
</style>
</head>
<body>

<div class="auth-brand">
  <a href="/">
    <img src="/images/logo_secradar.png" alt="SecRadar Azuron" style="height:180px;width:auto;object-fit:contain">
  </a>
</div>

<div class="auth-card">
  <h1 class="auth-title">Entrar na sua conta</h1>
  <p class="auth-sub">Seus dados sendo monitorados a todo momento.</p>

  @if($errors->any())
    <div class="flash-error">{{ $errors->first() }}</div>
  @endif

  <div class="attempts-warning" id="attempts-warning">
    ⚠ Múltiplas tentativas detectadas. Aguarde antes de tentar novamente.
  </div>

  <form method="POST" action="{{ route('login') }}" id="login-form">
    @csrf
    <div class="form-group">
      <label class="form-label">E-mail</label>
      <input
        type="email"
        name="email"
        class="form-input"
        placeholder="seu@email.com"
        value="{{ old('email') }}"
        maxlength="150"
        autocomplete="email"
        required
        autofocus>
    </div>
    <div class="form-group">
      <label class="form-label" style="display:flex;justify-content:space-between">
        Senha
        <a href="{{ route('password.request') }}" style="color:var(--accent);font-size:11px;text-transform:none;letter-spacing:0">Esqueci a senha</a>
      </label>
      <input
        type="password"
        name="password"
        class="form-input"
        placeholder="••••••••"
        maxlength="64"
        autocomplete="current-password"
        required>
    </div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px">
      <input type="checkbox" name="remember" id="remember" style="accent-color:var(--accent)">
      <label for="remember" style="font-size:13px;color:var(--muted);cursor:pointer">Manter conectado</label>
    </div>
    <button type="submit" class="btn-primary" id="submit-btn">Entrar</button>
  </form>

  <div class="divider">ou</div>

  <div class="auth-footer">
    Não tem conta? <a href="{{ route('register') }}">Criar conta grátis</a>
  </div>
</div>

<div class="marketing-strip">
  🛡 <strong>SecRadar</strong> monitora seus dados em tempo real — <strong>24h/dia, 7 dias por semana</strong>
</div>

<script>
// Proteção contra brute force no frontend
let attempts = parseInt(localStorage.getItem('login_attempts') || '0');
let lastAttempt = parseInt(localStorage.getItem('login_last') || '0');
const now = Date.now();

// Reset após 5 minutos
if (now - lastAttempt > 300000) {
  attempts = 0;
  localStorage.setItem('login_attempts', '0');
}

if (attempts >= 5) {
  document.getElementById('submit-btn').disabled = true;
  document.getElementById('attempts-warning').style.display = 'block';
  setTimeout(() => {
    attempts = 0;
    localStorage.setItem('login_attempts', '0');
    document.getElementById('submit-btn').disabled = false;
    document.getElementById('attempts-warning').style.display = 'none';
  }, 300000);
}

document.getElementById('login-form')?.addEventListener('submit', function() {
  attempts++;
  localStorage.setItem('login_attempts', attempts);
  localStorage.setItem('login_last', Date.now());
});
</script>
</body>
</html>
