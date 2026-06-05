<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#02191b">
<title>Criar conta — SecRadar Azuron</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#02191b;--bg2:#031f22;--bg3:#052428;--surface:#073035;--border:rgba(0,212,170,.12);--accent:#00d4aa;--danger:#ff4757;--safe:#2ed573;--text:#e8f4f2;--muted:#7aada6;--muted2:#4a7a75}
html,body{min-height:100%;background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;-webkit-font-smoothing:antialiased}
body{display:flex;flex-direction:column;align-items:center;justify-content:flex-start;min-height:100dvh;padding:32px 20px}
.auth-brand{text-align:center;margin-bottom:28px}
.auth-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px 24px;width:100%;max-width:480px}
@media(min-width:520px){.auth-card{padding:36px 40px}}
.auth-title{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:4px}
.auth-sub{font-size:13px;color:var(--muted);margin-bottom:24px}
.form-row{display:grid;grid-template-columns:1fr;gap:0}
@media(min-width:400px){.form-row{grid-template-columns:1fr 1fr;gap:0 14px}}
.form-group{margin-bottom:14px}
.form-label{display:flex;justify-content:space-between;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:5px}
.form-label-counter{font-size:10px;color:var(--muted2);letter-spacing:0;text-transform:none}
.form-input{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 14px;color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border .2s}
.form-input:focus{border-color:var(--accent)}
.form-input::placeholder{color:var(--muted2)}
.form-input.error{border-color:var(--danger)}
.plan-box{background:rgba(0,212,170,.07);border:1px solid rgba(0,212,170,.25);border-radius:10px;padding:14px 16px;margin-bottom:20px}
.plan-feature{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--muted);margin-bottom:4px}
.btn-primary{display:block;width:100%;padding:13px;background:var(--accent);color:var(--bg);border:none;border-radius:8px;font-size:15px;font-weight:600;font-family:'Rajdhani',sans-serif;letter-spacing:1px;cursor:pointer;transition:opacity .15s;text-transform:uppercase;margin-top:4px}
.btn-primary:hover{opacity:.85}
.auth-footer{text-align:center;margin-top:14px;font-size:13px;color:var(--muted)}
.auth-footer a{color:var(--accent)}
.flash-error{background:rgba(255,71,87,.12);border:1px solid rgba(255,71,87,.25);color:#ff6b78;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px}
.field-error{color:#ff6b78;font-size:11px;margin-top:3px}
.security-note{display:flex;align-items:center;gap:6px;margin-top:14px;font-size:11px;color:var(--muted2);text-align:center;justify-content:center}
.pw-strength{height:3px;border-radius:2px;margin-top:4px;transition:all .3s;background:var(--border)}
.phone-wrap{display:flex;align-items:center;background:var(--bg3);border:1px solid var(--border);border-radius:8px;overflow:hidden;transition:border .2s}
.phone-wrap:focus-within{border-color:var(--accent)}
.phone-prefix{padding:10px 12px;border-right:1px solid var(--border);flex-shrink:0;display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted)}
.phone-input{flex:1;background:transparent;border:none;padding:10px 14px;color:var(--text);font-size:14px;font-family:inherit;outline:none}
</style>
</head>
<body>

<div class="auth-brand">
  <a href="/">
    <img src="/images/logo_secradar.png" alt="SecRadar Azuron" style="height:165px;width:auto;object-fit:contain">
  </a>
</div>

<div class="auth-card">
  <h1 class="auth-title">Criar sua conta</h1>
  <p class="auth-sub">Comece a monitorar seus dados agora por R$ 29,90/mês.</p>

  <div class="plan-box">
    <div style="font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:700;color:var(--accent);margin-bottom:6px">🛡 O que você recebe</div>
    <div class="plan-feature"><span style="color:var(--safe)">✔</span> Monitoramento contínuo 24h/dia</div>
    <div class="plan-feature"><span style="color:var(--safe)">✔</span> Alertas instantâneos por e-mail e WhatsApp</div>
    <div class="plan-feature"><span style="color:var(--safe)">✔</span> Funciona em celular, tablet e computador</div>
  </div>

  @if($errors->any())
    <div class="flash-error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
    @csrf
    @if(isset($refCode) && $refCode)
      <input type="hidden" name="ref_code" value="{{ $refCode }}">
    @endif

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">
          Nome completo
          <span class="form-label-counter" id="name-counter">0/100</span>
        </label>
        <input type="text" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}"
          placeholder="João Silva"
          value="{{ old('name') }}"
          maxlength="100"
          minlength="3"
          autocomplete="name"
          id="name-input"
          required autofocus>
        @error('name')<div class="field-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">CPF <span style="color:var(--muted2);letter-spacing:0;text-transform:none;font-size:10px">(opcional)</span></label>
        <input type="text" name="cpf" class="form-input"
          placeholder="000.000.000-00"
          value="{{ old('cpf') }}"
          maxlength="14"
          id="cpf-input"
          inputmode="numeric"
          autocomplete="off">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">
        E-mail
        <span class="form-label-counter" id="email-counter">0/150</span>
      </label>
      <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
        placeholder="seu@email.com"
        value="{{ old('email') }}"
        maxlength="150"
        autocomplete="email"
        id="email-input"
        required>
      @error('email')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">WhatsApp <span style="color:var(--muted2);letter-spacing:0;text-transform:none;font-size:10px">(para alertas)</span></label>
      <div class="phone-wrap">
        <div class="phone-prefix">
          <span style="font-size:18px">🇧🇷</span>
          <span>+55</span>
        </div>
        <input type="text" name="phone" id="phone-input" class="phone-input"
          placeholder="(11) 99999-9999"
          maxlength="15"
          inputmode="numeric"
          autocomplete="tel"
          value="{{ old('phone') ? preg_replace('/^\+55/', '', old('phone')) : '' }}">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Senha</label>
        <input type="password" name="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}"
          placeholder="Mínimo 8 caracteres"
          maxlength="64"
          minlength="8"
          autocomplete="new-password"
          id="pw-input"
          required>
        <div class="pw-strength" id="pw-strength"></div>
        <div style="font-size:10px;color:var(--muted2);margin-top:2px" id="pw-hint"></div>
        @error('password')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label class="form-label">Confirmar senha</label>
        <input type="password" name="password_confirmation" class="form-input"
          placeholder="Repita a senha"
          maxlength="64"
          autocomplete="new-password"
          id="pw-confirm"
          required>
        <div style="font-size:10px;margin-top:2px" id="pw-match"></div>
      </div>
    </div>

    <button type="submit" class="btn-primary" id="submit-btn">Criar conta e proteger meus dados</button>
  </form>

  <div class="auth-footer" style="margin-top:14px">
    Já tem conta? <a href="{{ route('login') }}">Entrar</a>
  </div>
  <div class="security-note">🔒 Seus dados são criptografados e nunca compartilhados</div>
</div>

<script>
// Contador de caracteres
function setupCounter(inputId, counterId, max) {
  const input = document.getElementById(inputId);
  const counter = document.getElementById(counterId);
  if (!input || !counter) return;
  input.addEventListener('input', () => {
    const len = input.value.length;
    counter.textContent = len + '/' + max;
    counter.style.color = len > max * 0.9 ? 'var(--warn)' : '';
  });
}
setupCounter('name-input', 'name-counter', 100);
setupCounter('email-input', 'email-counter', 150);

// CPF mask
document.getElementById('cpf-input')?.addEventListener('input', function() {
  let v = this.value.replace(/\D/g,'').slice(0, 11);
  if(v.length > 3) v = v.slice(0,3)+'.'+v.slice(3);
  if(v.length > 7) v = v.slice(0,7)+'.'+v.slice(7);
  if(v.length > 11) v = v.slice(0,11)+'-'+v.slice(11,13);
  this.value = v;
});

// Phone mask
document.getElementById('phone-input')?.addEventListener('input', function() {
  let v = this.value.replace(/\D/g,'').slice(0, 11);
  if(v.length > 2) v = '('+v.slice(0,2)+') '+v.slice(2);
  if(v.length > 10) v = v.slice(0,10)+'-'+v.slice(10,14);
  this.value = v;
});

// Adiciona +55 antes de submeter
document.getElementById('register-form')?.addEventListener('submit', function() {
  const phoneInput = document.getElementById('phone-input');
  if(phoneInput && phoneInput.value) {
    phoneInput.value = '+55' + phoneInput.value.replace(/\D/g,'');
  }
});

// Força de senha
document.getElementById('pw-input')?.addEventListener('input', function() {
  const v = this.value;
  const bar = document.getElementById('pw-strength');
  const hint = document.getElementById('pw-hint');
  let score = 0;
  if(v.length >= 8) score++;
  if(/[A-Z]/.test(v)) score++;
  if(/[0-9]/.test(v)) score++;
  if(/[^A-Za-z0-9]/.test(v)) score++;

  const levels = [
    {color:'var(--danger)', width:'25%', text:'Muito fraca'},
    {color:'var(--warn)',   width:'50%', text:'Fraca'},
    {color:'#ffdd57',      width:'75%', text:'Boa'},
    {color:'var(--safe)',  width:'100%', text:'Forte'},
  ];
  const lvl = levels[Math.max(0, score-1)] || levels[0];
  bar.style.background = lvl.color;
  bar.style.width = v.length > 0 ? lvl.width : '0';
  hint.textContent = v.length > 0 ? lvl.text : '';
  hint.style.color = lvl.color;
});

// Confirmação de senha
document.getElementById('pw-confirm')?.addEventListener('input', function() {
  const pw = document.getElementById('pw-input').value;
  const el = document.getElementById('pw-match');
  if(this.value.length === 0) { el.textContent = ''; return; }
  if(this.value === pw) {
    el.textContent = '✔ Senhas conferem';
    el.style.color = 'var(--safe)';
  } else {
    el.textContent = '✖ Senhas não conferem';
    el.style.color = 'var(--danger)';
  }
});

// Bloqueia submit se senhas não conferem
document.getElementById('register-form')?.addEventListener('submit', function(e) {
  const pw  = document.getElementById('pw-input').value;
  const pwc = document.getElementById('pw-confirm').value;
  if(pw !== pwc) { e.preventDefault(); alert('As senhas não conferem.'); }
});
</script>
</body>
</html>
