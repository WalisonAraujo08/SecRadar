<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{margin:0;padding:0;background:#f4f4f4;font-family:'DM Sans','Helvetica Neue',Arial,sans-serif}
.wrap{max-width:580px;margin:32px auto;background:#02191b;border-radius:16px;overflow:hidden;border:1px solid rgba(0,212,170,.15)}
.header{background:#031f22;padding:28px 32px;border-bottom:1px solid rgba(0,212,170,.15);text-align:center}
.brand{font-size:22px;font-weight:700;letter-spacing:2px;color:#fff}
.brand-sub{font-size:10px;letter-spacing:3px;color:#00d4aa;display:block;margin-top:2px}
.body{padding:32px}
.alert-badge{display:inline-block;background:{{ $result->severity === 'critical' ? 'rgba(255,71,87,.2)' : 'rgba(255,165,2,.2)' }};color:{{ $result->severity === 'critical' ? '#ff4757' : '#ffa502' }};border:1px solid {{ $result->severity === 'critical' ? 'rgba(255,71,87,.3)' : 'rgba(255,165,2,.3)' }};padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:1px;margin-bottom:20px}
h1{font-size:22px;font-weight:700;color:#fff;margin:0 0 8px}
.sub{font-size:14px;color:#7aada6;line-height:1.6;margin:0 0 24px}
.info-box{background:#073035;border:1px solid rgba(0,212,170,.15);border-radius:10px;padding:20px;margin-bottom:24px}
.info-row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(0,212,170,.08);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-label{color:#7aada6}
.info-val{color:#e8f4f2;font-weight:500}
.cta{display:block;background:#00d4aa;color:#02191b;text-align:center;padding:14px 24px;border-radius:10px;font-size:15px;font-weight:700;text-decoration:none;margin:24px 0}
.steps{margin-bottom:24px}
.step{display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;font-size:13px;color:#7aada6;line-height:1.6}
.step-num{background:rgba(0,212,170,.15);color:#00d4aa;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;margin-top:2px}
.footer{background:#031f22;padding:20px 32px;text-align:center;border-top:1px solid rgba(0,212,170,.1)}
.footer-text{font-size:11px;color:#4a7a75;line-height:1.7}
.footer-brand{font-size:13px;color:#7aada6;margin-bottom:6px}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="brand">AZURON <span class="brand-sub">SECRADAR</span></div>
  </div>

  <div class="body">
    <div class="alert-badge">
      {{ $result->severity === 'critical' ? '⚠ ALERTA CRÍTICO' : '⚡ ALERTA DE SEGURANÇA' }}
    </div>

    <h1>{{ $user->name }}, seus dados foram detectados em um vazamento</h1>
    <p class="sub">
      O <strong style="color:#00d4aa">SecRadar</strong> identificou uma exposição de dados associada à sua conta.
      Aja agora para proteger seus acessos.
    </p>

    <div class="info-box">
      <div class="info-row">
        <span class="info-label">E-mail monitorado</span>
        <span class="info-val">{{ $result->monitoredEmail?->email ?? $user->email }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Severidade</span>
        <span class="info-val" style="color:{{ $result->severity === 'critical' ? '#ff4757' : '#ffa502' }}">{{ $result->severityLabel() }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Dados possivelmente expostos</span>
        <span class="info-val">{{ $result->dataExposedLabel() }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Detectado em</span>
        <span class="info-val">{{ $result->detected_at->format('d/m/Y \à\s H:i') }}</span>
      </div>
    </div>

    <p style="font-size:14px;font-weight:600;color:#fff;margin-bottom:12px">O que você deve fazer agora:</p>
    <div class="steps">
      <div class="step"><div class="step-num">1</div><div>Troque a senha do e-mail <strong style="color:#fff">{{ $result->monitoredEmail?->email ?? $user->email }}</strong> imediatamente</div></div>
      <div class="step"><div class="step-num">2</div><div>Ative a autenticação em dois fatores em todos os serviços que usam este e-mail</div></div>
      <div class="step"><div class="step-num">3</div><div>Monitore extratos bancários e cartões nos próximos dias</div></div>
      <div class="step"><div class="step-num">4</div><div>Acesse seu painel SecRadar para ver todos os detalhes</div></div>
    </div>

    <a href="{{ url('/painel/alertas') }}" class="cta">Ver detalhes no painel SecRadar</a>
  </div>

  <div class="footer">
    <div class="footer-brand">SecRadar · Azuron Tecnologia</div>
    <div class="footer-text">
      Você recebeu este e-mail porque está cadastrado no SecRadar.<br>
      <strong style="color:#00d4aa">Seus dados estão sendo monitorados 24h por dia.</strong>
    </div>
  </div>
</div>
</body>
</html>
