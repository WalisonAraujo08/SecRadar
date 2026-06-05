<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#02191b">
<meta name="description" content="SecRadar Azuron — Seus dados sendo monitorados a todo momento.">
<title>SecRadar Azuron — Proteção de Dados em Tempo Real</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#02191b;--bg2:#031f22;--bg3:#052428;--surface:#073035;--surface2:#0a3a40;
  --border:rgba(0,212,170,.12);--border2:rgba(0,212,170,.25);
  --accent:#00d4aa;--accent2:#00a8ff;
  --danger:#ff4757;--warn:#ffa502;--safe:#2ed573;
  --text:#e8f4f2;--muted:#7aada6;--muted2:#4a7a75;
  --font-brand:'Rajdhani',sans-serif;--font-body:'DM Sans',sans-serif;
}
html{scroll-behavior:smooth}
body{background:var(--bg);color:var(--text);font-family:var(--font-body);-webkit-font-smoothing:antialiased;overflow-x:hidden}
a{text-decoration:none}
.nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(2,25,27,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between}
.nav-brand{display:flex;align-items:center;gap:8px}
.nav-links{display:flex;align-items:center;gap:8px}
.nav-link{font-size:13px;color:var(--muted);padding:6px 12px;border-radius:6px;transition:.15s;display:none}
@media(min-width:600px){.nav-link{display:block}}
.nav-link:hover{color:var(--text)}
.btn-nav{background:var(--accent);color:var(--bg);padding:8px 18px;border-radius:7px;font-size:13px;font-weight:600;font-family:var(--font-brand);letter-spacing:1px;text-transform:uppercase;transition:.15s}
.btn-nav:hover{opacity:.85;color:var(--bg)}
.hero{padding:120px 20px 80px;text-align:center;max-width:800px;margin:0 auto}
.hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.2);border-radius:20px;padding:6px 14px;font-size:12px;letter-spacing:1px;color:var(--accent);font-weight:600;margin-bottom:24px}
.hero-eyebrow-dot{width:7px;height:7px;border-radius:50%;background:var(--safe);animation:pulse-dot 1.5s ease-in-out infinite}
@keyframes pulse-dot{0%,100%{opacity:1}50%{opacity:.4}}
.hero-title{font-family:var(--font-brand);font-size:clamp(36px,8vw,72px);font-weight:700;line-height:1.05;color:#fff;margin-bottom:20px}
.hero-title span{color:var(--accent)}
.hero-sub{font-size:clamp(15px,2.5vw,19px);color:var(--muted);line-height:1.7;max-width:540px;margin:0 auto 36px}
.hero-cta{display:flex;flex-direction:column;align-items:center;gap:12px}
@media(min-width:480px){.hero-cta{flex-direction:row;justify-content:center}}
.btn-hero{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:10px;font-size:16px;font-weight:600;font-family:var(--font-brand);letter-spacing:1px;text-transform:uppercase;transition:.15s;cursor:pointer;border:none}
.btn-hero-primary{background:var(--accent);color:var(--bg)}
.btn-hero-primary:hover{opacity:.85;color:var(--bg)}
.btn-hero-outline{background:transparent;color:var(--text);border:1px solid var(--border2)}
.btn-hero-outline:hover{background:var(--surface);color:var(--text)}
.hero-note{font-size:12px;color:var(--muted2);margin-top:8px}
.stats-bar{background:var(--bg2);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:24px 20px}
.stats-bar-inner{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:900px;margin:0 auto;text-align:center}
@media(min-width:640px){.stats-bar-inner{grid-template-columns:repeat(4,1fr)}}
.stat-num{font-family:var(--font-brand);font-size:32px;font-weight:700;color:var(--accent)}
.stat-txt{font-size:12px;color:var(--muted);margin-top:2px}
.section{padding:80px 20px}
.section-inner{max-width:1100px;margin:0 auto}
.section-label{font-family:var(--font-brand);font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--accent);margin-bottom:12px;display:block}
.section-title{font-family:var(--font-brand);font-size:clamp(26px,4vw,40px);font-weight:700;color:#fff;margin-bottom:14px;line-height:1.1}
.section-sub{font-size:15px;color:var(--muted);max-width:540px;line-height:1.7;margin-bottom:48px}
.steps{display:grid;grid-template-columns:1fr;gap:24px}
@media(min-width:768px){.steps{grid-template-columns:repeat(3,1fr)}}
.step{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px 24px}
.step-num{font-family:var(--font-brand);font-size:36px;font-weight:700;color:var(--accent);opacity:.3;margin-bottom:12px}
.step-title{font-family:var(--font-brand);font-size:17px;font-weight:700;color:#fff;margin-bottom:8px}
.step-text{font-size:14px;color:var(--muted);line-height:1.6}
.features{display:grid;grid-template-columns:1fr;gap:16px}
@media(min-width:640px){.features{grid-template-columns:1fr 1fr}}
@media(min-width:1000px){.features{grid-template-columns:repeat(3,1fr)}}
.feature{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px}
.feature-icon{width:40px;height:40px;border-radius:10px;background:rgba(0,212,170,.12);display:flex;align-items:center;justify-content:center;margin-bottom:14px;font-size:18px}
.feature-title{font-family:var(--font-brand);font-size:15px;font-weight:700;color:#fff;margin-bottom:6px}
.feature-text{font-size:13px;color:var(--muted);line-height:1.6}
.urgency{background:rgba(255,71,87,.06);border:1px solid rgba(255,71,87,.2);border-radius:16px;padding:36px;text-align:center;margin:0 auto;max-width:700px}
.urgency-title{font-family:var(--font-brand);font-size:clamp(22px,4vw,32px);font-weight:700;color:var(--danger);margin-bottom:12px}
.urgency-text{font-size:15px;color:var(--muted);line-height:1.7;margin-bottom:24px}
.urgency-num{font-family:var(--font-brand);font-size:56px;font-weight:700;color:var(--danger);line-height:1}
.pricing-grid{display:grid;grid-template-columns:1fr;gap:20px;max-width:800px;margin:0 auto}
@media(min-width:768px){.pricing-grid{grid-template-columns:1fr 1fr}}
.pricing-card{background:var(--surface);border:1px solid var(--border2);border-radius:20px;padding:32px;position:relative;overflow:hidden}
.pricing-price{font-family:var(--font-brand);font-size:56px;font-weight:700;color:#fff;line-height:1}
.pricing-period{font-size:14px;color:var(--muted);margin-bottom:24px}
.pricing-features{text-align:left;margin-bottom:28px;display:flex;flex-direction:column;gap:10px}
.pricing-feature{display:flex;align-items:center;gap:10px;font-size:14px;color:var(--muted)}
.pricing-feature-check{color:var(--safe);font-size:14px;flex-shrink:0}
.partner-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
@media(min-width:640px){.partner-grid{grid-template-columns:repeat(4,1fr)}}
.testimonials{display:grid;grid-template-columns:1fr;gap:16px}
@media(min-width:640px){.testimonials{grid-template-columns:1fr 1fr}}
@media(min-width:900px){.testimonials{grid-template-columns:repeat(3,1fr)}}
.testimonial{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px}
.testimonial-text{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:16px;font-style:italic}
.testimonial-author{display:flex;align-items:center;gap:10px}
.testimonial-avatar{width:32px;height:32px;border-radius:50%;background:rgba(0,212,170,.15);display:flex;align-items:center;justify-content:center;font-family:var(--font-brand);font-size:12px;font-weight:700;color:var(--accent)}
.testimonial-name{font-size:13px;font-weight:500;color:#fff}
.testimonial-role{font-size:11px;color:var(--muted2)}
.faq-item{border-bottom:1px solid var(--border);padding:16px 0}
.faq-q{font-size:15px;font-weight:500;color:#fff;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;user-select:none}
.faq-a{font-size:14px;color:var(--muted);line-height:1.7;padding-top:10px;display:none}
.faq-a.open{display:block}
.faq-icon{transition:.2s;flex-shrink:0;color:var(--accent)}
.cta-bottom{background:linear-gradient(135deg,rgba(0,212,170,.1),rgba(0,168,255,.06));border:1px solid rgba(0,212,170,.2);border-radius:20px;padding:60px 36px;text-align:center;max-width:700px;margin:0 auto}
.cta-bottom-title{font-family:var(--font-brand);font-size:clamp(24px,4vw,36px);font-weight:700;color:#fff;margin-bottom:12px}
.cta-bottom-sub{font-size:15px;color:var(--muted);margin-bottom:28px;line-height:1.6}
footer{background:var(--bg2);border-top:1px solid var(--border);padding:32px 20px;text-align:center}
.footer-text{font-size:12px;color:var(--muted2);line-height:1.8}
</style>
</head>
<body>

{{-- NAV --}}
<nav class="nav">
  <a href="/" class="nav-brand">
    <img src="/images/logo_secradar.png" alt="SecRadar Azuron" style="height:120px;width:auto;object-fit:contain">
  </a>
  <div class="nav-links">
    <a href="#como-funciona" class="nav-link">Como funciona</a>
    <a href="#precos" class="nav-link">Preços</a>
    <a href="#parceiros" class="nav-link">Parceiros</a>
    <a href="{{ route('login') }}" class="nav-link">Entrar</a>
    <a href="{{ route('register') }}" class="btn-nav">Começar agora</a>
  </div>
</nav>

{{-- HERO --}}
<section class="hero">
  <div class="hero-eyebrow">
    <span class="hero-eyebrow-dot"></span>
    Monitoramento ativo agora
  </div>
  <h1 class="hero-title">
    Seus dados estão sendo<br><span>monitorados a todo momento</span>
  </h1>
  <p class="hero-sub">
    Enquanto você dorme, o SecRadar rastreia centenas de bases comprometidas para alertar você <strong style="color:#fff">antes que criminosos usem seus dados.</strong>
  </p>
  <div class="hero-cta">
    <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">🛡 Proteger meus dados agora</a>
    <a href="#como-funciona" class="btn-hero btn-hero-outline">Ver como funciona</a>
  </div>
  <p class="hero-note">A partir de R$ 29,90/mês · Cancele quando quiser · Funciona no celular</p>
</section>

{{-- STATS --}}
<div class="stats-bar">
  <div class="stats-bar-inner">
    <div><div class="stat-num">16B+</div><div class="stat-txt">dados vazados em 2025</div></div>
    <div><div class="stat-num">24h</div><div class="stat-txt">monitoramento contínuo</div></div>
    <div><div class="stat-num">&lt;1min</div><div class="stat-txt">tempo médio de alerta</div></div>
    <div><div class="stat-num">R$29,90</div><div class="stat-txt">proteção a partir de</div></div>
  </div>
</div>

{{-- URGENCY --}}
<section class="section" id="risco">
  <div class="section-inner" style="text-align:center">
    <div class="urgency">
      <div class="urgency-num">1 em cada 3</div>
      <div class="urgency-title" style="margin-top:8px">brasileiros já teve dados vazados</div>
      <p class="urgency-text">Seus dados podem estar circulando na dark web <strong style="color:#fff">neste exato momento</strong> — e você não sabe. E-mails, senhas, CPF e dados bancários são vendidos por criminosos sem que você perceba.</p>
      <a href="{{ route('register') }}" class="btn-hero btn-hero-primary" style="display:inline-flex">Verificar meus dados agora</a>
    </div>
  </div>
</section>

{{-- HOW IT WORKS --}}
<section class="section" id="como-funciona" style="background:var(--bg2)">
  <div class="section-inner">
    <span class="section-label">Como funciona</span>
    <h2 class="section-title">Proteção ativa sem complicação</h2>
    <p class="section-sub">Em 3 passos simples, você fica protegido 24h por dia.</p>
    <div class="steps">
      <div class="step"><div class="step-num">01</div><div class="step-title">Cadastro em 2 minutos</div><p class="step-text">Informe seu e-mail, CPF e WhatsApp. Nossa tecnologia começa a monitorar imediatamente após a ativação.</p></div>
      <div class="step"><div class="step-num">02</div><div class="step-title">SecRadar vigia por você</div><p class="step-text">Nosso sistema rastreia continuamente centenas de bases de dados comprometidas, 24 horas por dia, 7 dias por semana.</p></div>
      <div class="step"><div class="step-num">03</div><div class="step-title">Alerta imediato</div><p class="step-text">Ao detectar qualquer exposição, você recebe um alerta instantâneo por e-mail E WhatsApp com o que fazer.</p></div>
    </div>
  </div>
</section>

{{-- FEATURES --}}
<section class="section">
  <div class="section-inner">
    <span class="section-label">Recursos</span>
    <h2 class="section-title">Tudo que você precisa para ficar seguro</h2>
    <div class="features">
      @foreach([
        ['🔍','Varredura em Tempo Real','Nossa tecnologia varre continuamente bases comprometidas — você é avisado em minutos.'],
        ['📱','Funciona no Celular','Painel 100% responsivo. Use no celular, tablet ou computador — sem instalar nada.'],
        ['📧','Alertas por WhatsApp','Receba notificações instantâneas pelo WhatsApp assim que detectarmos algo.'],
        ['🛡','Cadeado de Status','Saiba de relance se seus dados estão seguros ou em risco com nosso indicador visual.'],
        ['👨‍👩‍👧','Proteja sua família','Adicione e-mails de familiares por apenas R$ 8,90/mês cada e monitore a família inteira.'],
        ['📊','Histórico completo','Veja todos os incidentes detectados, dados expostos e quando aconteceu.'],
      ] as [$icon,$title,$text])
        <div class="feature">
          <div class="feature-icon">{{ $icon }}</div>
          <div class="feature-title">{{ $title }}</div>
          <p class="feature-text">{{ $text }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- PRICING --}}
<section class="section" id="precos" style="background:var(--bg2)">
  <div class="section-inner" style="text-align:center">
    <span class="section-label">Preços</span>
    <h2 class="section-title">Escolha o plano ideal para você</h2>
    <p class="section-sub" style="margin:0 auto 48px">Sem contratos longos. Cancele quando quiser.</p>

    <div class="pricing-grid">
      {{-- Pessoal --}}
      <div class="pricing-card">
        <div style="font-family:var(--font-brand);font-size:12px;letter-spacing:2px;color:var(--accent);margin-bottom:12px">PESSOAL</div>
        <div class="pricing-price">R$ 29<span style="font-size:28px">,90</span></div>
        <div class="pricing-period">por mês</div>
        <div class="pricing-features">
          @foreach(['1 e-mail monitorado','CPF + telefone incluídos','Alertas e-mail + WhatsApp','Varredura automática 24h','Até 5 e-mails extras'] as $f)
            <div class="pricing-feature"><span class="pricing-feature-check">✔</span>{{ $f }}</div>
          @endforeach
          <div class="pricing-feature" style="color:var(--accent2)"><span style="color:var(--accent2)">+</span>E-mails extras por R$ 8,90/mês</div>
        </div>
        <a href="{{ route('register') }}" class="btn-hero btn-hero-outline" style="display:flex;justify-content:center;font-size:14px">Começar agora</a>
      </div>

      {{-- Corporativo --}}
      <div class="pricing-card" style="border:2px solid rgba(0,168,255,.5)">
        <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--accent2);color:var(--bg);font-family:var(--font-brand);font-size:10px;font-weight:700;letter-spacing:2px;padding:4px 16px;border-radius:20px;white-space:nowrap">MAIS POPULAR</div>
        <div style="font-family:var(--font-brand);font-size:12px;letter-spacing:2px;color:var(--accent2);margin-bottom:12px">CORPORATIVO</div>
        <div class="pricing-price">R$ 149<span style="font-size:28px">,90</span></div>
        <div class="pricing-period">por mês — preço fixo</div>
        <div class="pricing-features">
          @foreach(['Até 20 e-mails corporativos','CPF + telefone de cada membro','Alertas e-mail + WhatsApp','Varredura automática 24h','Suporte prioritário'] as $f)
            <div class="pricing-feature"><span class="pricing-feature-check" style="color:var(--accent2)">✔</span>{{ $f }}</div>
          @endforeach
        </div>
        <a href="{{ route('register') }}" class="btn-hero btn-hero-primary" style="display:flex;justify-content:center;font-size:14px;background:var(--accent2)">Começar agora</a>
      </div>
    </div>

    <p style="font-size:12px;color:var(--muted2);margin-top:24px">🔒 Pagamento seguro via Mercado Pago · Cancele quando quiser</p>
  </div>
</section>

{{-- TESTIMONIALS --}}
<section class="section">
  <div class="section-inner">
    <span class="section-label">Depoimentos</span>
    <h2 class="section-title">O que nossos clientes dizem</h2>
    <div class="testimonials">
      @foreach([
        ['"Recebi o alerta em menos de 5 minutos. Consegui trocar todas as senhas antes que qualquer dano fosse feito."','M.S.','Professora, SP'],
        ['"Vale muito mais do que o preço. A paz de espírito de saber que alguém está monitorando por mim é impagável."','R.F.','Empresário, RJ'],
        ['"Adicionei minha esposa e meus dois filhos. Toda a família protegida por menos de R$ 70/mês."','C.A.','Engenheiro, MG'],
      ] as [$t,$n,$r])
        <div class="testimonial">
          <div style="color:var(--accent);margin-bottom:8px">★★★★★</div>
          <p class="testimonial-text">{{ $t }}</p>
          <div class="testimonial-author">
            <div class="testimonial-avatar">{{ $n[1] }}</div>
            <div><div class="testimonial-name">{{ $n }}</div><div class="testimonial-role">{{ $r }}</div></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- FAQ --}}
<section class="section">
  <div class="section-inner" style="max-width:700px">
    <span class="section-label">FAQ</span>
    <h2 class="section-title">Perguntas frequentes</h2>
    @foreach([
      ['Como o SecRadar detecta vazamentos?','Nossa tecnologia proprietária monitora continuamente múltiplas bases de dados de incidentes de segurança. Ao detectar qualquer correspondência com seus dados, você é alertado imediatamente.'],
      ['Precisa instalar algum aplicativo?','Não. O SecRadar funciona 100% pelo navegador — funciona perfeitamente em qualquer celular, tablet ou computador, sem instalar nada.'],
      ['Posso monitorar e-mails de familiares?','Sim! Adicione o e-mail de cônjuge, filhos ou qualquer familiar por apenas R$ 8,90/mês cada, na mesma conta.'],
      ['É seguro informar meu CPF e e-mail?','Sim. Seus dados são armazenados com criptografia e usados exclusivamente para o monitoramento. Nunca compartilhamos seus dados com terceiros.'],
      ['Posso cancelar quando quiser?','Sim. Cancele a qualquer momento pelo próprio painel, sem multa ou burocracia.'],
      ['Como funciona o programa de parceiros?','Após aprovação, você recebe um link exclusivo. Cada pessoa que assinar pelo seu link gera comissão mensal para você, enquanto permanecer ativo.'],
    ] as [$q,$a])
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">{{ $q }}<span class="faq-icon">▾</span></div>
        <div class="faq-a">{{ $a }}</div>
      </div>
    @endforeach
  </div>
</section>

{{-- CTA BOTTOM --}}
<section class="section" style="background:var(--bg2)">
  <div class="cta-bottom">
    <div style="font-size:40px;margin-bottom:12px">🛡</div>
    <h2 class="cta-bottom-title">Seus dados podem estar em risco agora</h2>
    <p class="cta-bottom-sub">Mais de 16 bilhões de logins e senhas foram expostos em 2025. Não espere se tornar uma vítima para agir.</p>
    <a href="{{ route('register') }}" class="btn-hero btn-hero-primary" style="display:inline-flex">Proteger meus dados — R$ 29,90/mês</a>
    <p style="font-size:12px;color:var(--muted2);margin-top:14px">Sem contratos · Cancele quando quiser · Resultados em minutos</p>
  </div>
</section>

{{-- SEJA PARCEIRO --}}
<section class="section" id="parceiros">
  <div class="section-inner" style="max-width:800px;margin:0 auto">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;background:var(--surface);border:1px solid rgba(255,165,2,.2);border-radius:16px;padding:28px 32px">
      <div>
        <div style="font-family:var(--font-brand);font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--warn);margin-bottom:6px">Programa de Parceiros</div>
        <div style="font-family:var(--font-brand);font-size:22px;font-weight:700;color:#fff;margin-bottom:6px">Ganhe indicando o SecRadar</div>
        <p style="font-size:13px;color:var(--muted)">Comissões de 10% a 25% por indicado ativo · Pagamento via PIX · Sem limite de indicações</p>
      </div>
      <a href="{{ route('register') }}" class="btn-hero" style="background:rgba(255,165,2,.12);color:var(--warn);border:1px solid rgba(255,165,2,.3);font-size:14px;flex-shrink:0">
        💰 Saiba mais
      </a>
    </div>
  </div>
</section>

{{-- FOOTER --}}
<footer>
  <div style="margin-bottom:12px">
    <img src="/images/logo.png" alt="SecRadar Azuron" style="height:28px;width:auto;object-fit:contain;opacity:.8">
  </div>
  <div class="footer-text">
    © {{ date('Y') }} Azuron Tecnologia. Todos os direitos reservados.<br>
    <a href="{{ route('login') }}" style="color:var(--accent)">Entrar</a> ·
    <a href="{{ route('register') }}" style="color:var(--accent)">Criar conta</a> ·
    <a href="#parceiros" style="color:var(--warn)">Seja Parceiro</a>
  </div>
</footer>

<script>
function toggleFaq(el) {
  const answer = el.nextElementSibling;
  const icon = el.querySelector('.faq-icon');
  const isOpen = answer.classList.contains('open');
  document.querySelectorAll('.faq-a').forEach(a => a.classList.remove('open'));
  document.querySelectorAll('.faq-icon').forEach(i => i.style.transform = '');
  if (!isOpen) { answer.classList.add('open'); icon.style.transform = 'rotate(180deg)'; }
}
</script>
</body>
</html>
