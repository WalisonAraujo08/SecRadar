<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#02191b">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SecRadar">
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<title>@yield('title', 'SecRadar') — Azuron</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ─── RESET & BASE ─── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:        #02191b;
  --bg2:       #031f22;
  --bg3:       #052428;
  --surface:   #073035;
  --surface2:  #0a3a40;
  --border:    rgba(0,212,170,0.12);
  --border2:   rgba(0,212,170,0.25);
  --accent:    #00d4aa;
  --accent2:   #00a8ff;
  --danger:    #ff4757;
  --warn:      #ffa502;
  --safe:      #2ed573;
  --text:      #e8f4f2;
  --muted:     #7aada6;
  --muted2:    #4a7a75;
  --white:     #ffffff;
  --radius:    10px;
  --radius-lg: 16px;
  --font-brand:'Rajdhani',sans-serif;
  --font-body: 'DM Sans',sans-serif;
}
html,body{height:100%;background:var(--bg);color:var(--text);font-family:var(--font-body);font-size:15px;line-height:1.6;-webkit-font-smoothing:antialiased}
a{color:var(--accent);text-decoration:none}
a:hover{opacity:.8}
img{max-width:100%;height:auto}
button,input,select,textarea{font-family:inherit}
::selection{background:rgba(0,212,170,.25)}

/* ─── SCROLLBAR ─── */
::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--surface2);border-radius:2px}

/* ─── LAYOUT SHELL ─── */
.app-shell{display:flex;min-height:100dvh;flex-direction:column}
@media(min-width:768px){.app-shell{flex-direction:row}}

/* ─── SIDEBAR ─── */
.sidebar{
  background:var(--bg2);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 16px;height:56px;flex-shrink:0;position:sticky;top:0;z-index:100;
}
@media(min-width:768px){
  .sidebar{
    width:220px;height:100dvh;flex-direction:column;justify-content:flex-start;
    border-bottom:none;border-right:1px solid var(--border);
    padding:24px 0;position:fixed;left:0;top:0;overflow-y:auto;
  }
}

.sidebar-brand{
  display:flex;align-items:center;gap:10px;
  font-family:var(--font-brand);font-size:20px;font-weight:700;
  letter-spacing:2px;color:var(--white);
  padding:0 20px 0 16px;
}
@media(min-width:768px){.sidebar-brand{margin-bottom:32px;padding:0 20px}}

.sidebar-brand svg{width:26px;height:26px;flex-shrink:0}

.sidebar-sub{
  font-family:var(--font-brand);font-size:10px;
  letter-spacing:3px;color:var(--accent);font-weight:600;
  margin-top:-2px;display:block;
}

/* ─── NAV ─── */
.nav-menu{
  display:none;
  flex-direction:column;gap:2px;width:100%;padding:0 8px;
}
@media(min-width:768px){.nav-menu{display:flex}}

.nav-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:8px;
  font-size:13px;font-weight:500;color:var(--muted);
  text-decoration:none;transition:all .15s;position:relative;
}
.nav-item:hover{background:var(--surface);color:var(--text)}
.nav-item.active{background:rgba(0,212,170,.1);color:var(--accent)}
.nav-item svg{width:18px;height:18px;flex-shrink:0}

.nav-badge{
  margin-left:auto;background:var(--danger);color:#fff;
  font-size:10px;font-weight:700;padding:1px 6px;border-radius:20px;
}

/* ─── MOBILE NAV HAMBURGER ─── */
.mob-nav-toggle{
  display:flex;flex-direction:column;gap:4px;
  background:none;border:none;cursor:pointer;padding:8px;
}
@media(min-width:768px){.mob-nav-toggle{display:none}}
.mob-nav-toggle span{display:block;width:20px;height:2px;background:var(--muted);border-radius:1px;transition:.2s}

.mob-nav{
  display:none;position:fixed;inset:56px 0 0 0;background:var(--bg2);
  z-index:99;padding:16px;flex-direction:column;gap:4px;overflow-y:auto;
}
.mob-nav.open{display:flex}
@media(min-width:768px){.mob-nav{display:none!important}}

/* ─── MAIN CONTENT ─── */
.main-content{
  flex:1;padding:16px;
  min-height:calc(100dvh - 56px);
}
@media(min-width:768px){
  .main-content{margin-left:220px;padding:28px 32px;min-height:100dvh}
}
@media(min-width:1200px){.main-content{padding:32px 40px}}

/* ─── PAGE HEADER ─── */
.page-header{margin-bottom:24px}
.page-title{
  font-family:var(--font-brand);font-size:26px;font-weight:700;
  letter-spacing:.5px;color:var(--white);line-height:1.1;
}
@media(min-width:768px){.page-title{font-size:30px}}
.page-sub{font-size:13px;color:var(--muted);margin-top:4px}

/* ─── CARDS ─── */
.card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-lg);padding:20px;
}
.card-title{
  font-family:var(--font-brand);font-size:12px;font-weight:600;
  letter-spacing:1.5px;text-transform:uppercase;color:var(--accent);
  margin-bottom:16px;
}

/* ─── STAT CARDS ─── */
.stats-grid{
  display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;
}
@media(min-width:768px){.stats-grid{grid-template-columns:repeat(4,1fr)}}

.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px}
.stat-label{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.stat-value{font-family:var(--font-brand);font-size:32px;font-weight:700;line-height:1}
.stat-value.danger{color:var(--danger)}
.stat-value.warn{color:var(--warn)}
.stat-value.safe{color:var(--safe)}
.stat-value.info{color:var(--accent2)}
.stat-sub{font-size:11px;color:var(--muted);margin-top:4px}

/* ─── BUTTONS ─── */
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  padding:10px 20px;border-radius:8px;font-weight:500;font-size:14px;
  cursor:pointer;transition:all .15s;border:none;text-decoration:none;
  white-space:nowrap;
}
.btn-primary{background:var(--accent);color:var(--bg)}
.btn-primary:hover{opacity:.85;color:var(--bg)}
.btn-outline{background:transparent;color:var(--accent);border:1px solid rgba(0,212,170,.35)}
.btn-outline:hover{background:rgba(0,212,170,.08);color:var(--accent)}
.btn-danger{background:rgba(255,71,87,.15);color:var(--danger);border:1px solid rgba(255,71,87,.25)}
.btn-danger:hover{background:rgba(255,71,87,.25)}
.btn-sm{padding:6px 14px;font-size:12px}
.btn:disabled{opacity:.5;cursor:not-allowed}
.btn-full{width:100%}

/* ─── FORMS ─── */
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-input{
  width:100%;background:var(--bg3);border:1px solid var(--border);
  border-radius:8px;padding:10px 14px;color:var(--text);font-size:14px;
  transition:border .2s;outline:none;
}
.form-input:focus{border-color:var(--accent)}
.form-input::placeholder{color:var(--muted2)}
.form-grid{display:grid;grid-template-columns:1fr;gap:0}
@media(min-width:640px){.form-grid.cols-2{grid-template-columns:1fr 1fr;gap:0 16px}}

/* ─── BADGES / PILLS ─── */
.pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:.3px}
.pill-critical{background:rgba(255,71,87,.2);color:#ff6b78}
.pill-high{background:rgba(255,100,30,.2);color:#ff8040}
.pill-medium{background:rgba(255,165,2,.2);color:#ffbb33}
.pill-low{background:rgba(46,213,115,.2);color:var(--safe)}
.pill-safe{background:rgba(46,213,115,.15);color:var(--safe)}
.pill-info{background:rgba(0,168,255,.15);color:var(--accent2)}

/* ─── ALERTS LIST ─── */
.alert-item{
  display:flex;gap:12px;align-items:flex-start;
  padding:14px 0;border-bottom:1px solid rgba(0,212,170,.06);
}
.alert-item:last-child{border-bottom:none}
.alert-icon{
  width:36px;height:36px;border-radius:8px;display:flex;
  align-items:center;justify-content:center;flex-shrink:0;font-size:16px;
}
.alert-icon.critical{background:rgba(255,71,87,.15)}
.alert-icon.high{background:rgba(255,100,30,.15)}
.alert-icon.medium{background:rgba(255,165,2,.15)}
.alert-icon.low{background:rgba(0,168,255,.1)}

/* ─── LOCK STATUS WIDGET (substitui ícone da bandeja) ─── */
.lock-widget{
  position:fixed;bottom:20px;right:20px;z-index:1000;
  width:52px;height:52px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 4px 20px rgba(0,0,0,.4);cursor:pointer;
  transition:transform .2s;
  border:2px solid rgba(255,255,255,.1);
}
.lock-widget:hover{transform:scale(1.08)}
.lock-widget.safe{background:rgba(46,213,115,.2);border-color:var(--safe)}
.lock-widget.alert{background:rgba(255,71,87,.2);border-color:var(--danger)}
.lock-widget.scanning{background:rgba(255,165,2,.15);border-color:var(--warn)}
.lock-widget svg{width:24px;height:24px}
.lock-dot{
  position:absolute;top:2px;right:2px;
  width:12px;height:12px;border-radius:50%;
  border:2px solid var(--bg);
}
.lock-dot.safe{background:var(--safe)}
.lock-dot.alert{background:var(--danger);animation:pulse-dot 1.2s ease-in-out infinite}
.lock-dot.scanning{background:var(--warn);animation:pulse-dot .8s ease-in-out infinite}
@keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)}}
.lock-tooltip{
  position:absolute;right:60px;bottom:50%;transform:translateY(50%);
  background:var(--surface);border:1px solid var(--border);
  border-radius:8px;padding:6px 12px;font-size:12px;white-space:nowrap;
  pointer-events:none;opacity:0;transition:opacity .2s;color:var(--text);
  box-shadow:0 4px 16px rgba(0,0,0,.3);
}
.lock-widget:hover .lock-tooltip{opacity:1}

/* ─── FLASH MESSAGES ─── */
.flash{
  padding:12px 16px;border-radius:8px;margin-bottom:16px;
  font-size:13px;display:flex;align-items:center;gap:10px;
}
.flash-success{background:rgba(46,213,115,.12);border:1px solid rgba(46,213,115,.25);color:var(--safe)}
.flash-error{background:rgba(255,71,87,.12);border:1px solid rgba(255,71,87,.25);color:#ff6b78}
.flash-info{background:rgba(0,168,255,.1);border:1px solid rgba(0,168,255,.2);color:var(--accent2)}

/* ─── SIDEBAR USER ─── */
.sidebar-user{
  margin-top:auto;padding:16px 12px;width:100%;
  border-top:1px solid var(--border);
}
.sidebar-user-info{display:flex;align-items:center;gap:10px}
.sidebar-avatar{
  width:32px;height:32px;border-radius:50%;background:rgba(0,212,170,.2);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--font-brand);font-size:13px;font-weight:700;color:var(--accent);
  flex-shrink:0;
}
.sidebar-username{font-size:12px;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sidebar-plan{font-size:10px;color:var(--muted)}

/* ─── SCAN PROGRESS BAR ─── */
.scan-progress{
  height:3px;background:var(--bg3);border-radius:2px;overflow:hidden;margin:8px 0;
}
.scan-progress-fill{
  height:100%;background:linear-gradient(90deg,var(--accent),var(--accent2));
  border-radius:2px;transition:width .4s ease;width:0%;
}

/* ─── UTILITY ─── */
.grid-2{display:grid;grid-template-columns:1fr;gap:16px}
@media(min-width:768px){.grid-2{grid-template-columns:1fr 1fr}}
.grid-3{display:grid;grid-template-columns:1fr;gap:16px}
@media(min-width:768px){.grid-3{grid-template-columns:1fr 1fr 1fr}}
.text-center{text-align:center}
.text-muted{color:var(--muted)}
.text-accent{color:var(--accent)}
.text-danger{color:var(--danger)}
.text-safe{color:var(--safe)}
.mt-4{margin-top:16px}.mt-6{margin-top:24px}.mt-8{margin-top:32px}
.mb-4{margin-bottom:16px}.mb-6{margin-bottom:24px}
.gap-2{gap:8px}.gap-3{gap:12px}
.flex{display:flex}.items-center{align-items:center}.justify-between{justify-content:space-between}
.w-full{width:100%}
.divider{height:1px;background:var(--border);margin:16px 0}
.chip{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--surface2);border:1px solid var(--border);
  border-radius:20px;padding:4px 10px;font-size:12px;color:var(--text);
}
.chip-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}

/* ─── RESPONSIVE TABLE ─── */
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:13px}
th{background:var(--bg3);color:var(--muted);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;padding:10px 14px;text-align:left;font-weight:500}
td{padding:12px 14px;border-bottom:1px solid rgba(0,212,170,.05);color:var(--text);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(0,212,170,.02)}

/* ─── EMPTY STATE ─── */
.empty-state{text-align:center;padding:48px 24px}
.empty-state-icon{font-size:48px;margin-bottom:12px;opacity:.3}
.empty-state-title{font-family:var(--font-brand);font-size:18px;color:var(--muted);margin-bottom:6px}
.empty-state-sub{font-size:13px;color:var(--muted2)}

/* ─── LOADING SPINNER ─── */
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:18px;height:18px;border:2px solid rgba(0,212,170,.2);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
</style>
@stack('styles')
</head>
<body>
<div class="app-shell">

  {{-- SIDEBAR (desktop) / TOP NAV (mobile) --}}
  <nav class="sidebar">
    <a href="{{ route('client.dashboard') }}" class="sidebar-brand">
  <img src="/images/logo_secradar.png" alt="SecRadar Azuron" style="height:120px;width:auto;object-fit:contain">
</a>

    {{-- Desktop Nav --}}
    <ul class="nav-menu" style="list-style:none">
      <li>
        <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </a>
      </li>
      <li>
        <a href="{{ route('client.scan.index') }}" class="nav-item {{ request()->routeIs('client.scan*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          Varredura
        </a>
      </li>
      <li>
        <a href="{{ route('client.emails.index') }}" class="nav-item {{ request()->routeIs('client.emails*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          E-mails Monitorados
        </a>
      </li>
      <li>
        <a href="{{ route('client.alerts.index') }}" class="nav-item {{ request()->routeIs('client.alerts*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          Alertas
          @php $unread = auth()->user()->unreadAlertsCount(); @endphp
          @if($unread > 0)<span class="nav-badge">{{ $unread }}</span>@endif
        </a>
      </li>
      <li>
        <a href="{{ route('subscription.index') }}" class="nav-item {{ request()->routeIs('subscription*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Minha Assinatura
        </a>
      </li>
    </ul>

    {{-- Sidebar user info (desktop) --}}
    <div class="sidebar-user" style="display:none" id="sidebar-user-desktop">
      <div class="sidebar-user-info">
        <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        <div style="overflow:hidden">
          <div class="sidebar-username">{{ auth()->user()->name }}</div>
          <div class="sidebar-plan">Plano SecRadar</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm btn-full" style="margin-top:0">Sair</button>
      </form>
    </div>

    {{-- Mobile: hamburger + logout --}}
    <div style="display:flex;align-items:center;gap:12px" class="md-hide">
      @php $unreadMob = auth()->user()->unreadAlertsCount(); @endphp
      @if($unreadMob > 0)
        <a href="{{ route('client.alerts.index') }}" style="position:relative">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span class="nav-badge" style="position:absolute;top:-6px;right:-6px">{{ $unreadMob }}</span>
        </a>
      @endif
      <button class="mob-nav-toggle" id="mob-toggle" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  {{-- Mobile Nav Overlay --}}
<div class="mob-nav" id="mob-nav">
  <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    Dashboard
  </a>
  <a href="{{ route('client.scan.index') }}" class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
    Varredura
  </a>
  <a href="{{ route('client.emails.index') }}" class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    E-mails Monitorados
  </a>
  <a href="{{ route('client.alerts.index') }}" class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    Alertas @if($unreadMob > 0)<span class="nav-badge">{{ $unreadMob }}</span>@endif
  </a>
  <a href="{{ route('subscription.index') }}" class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    Minha Assinatura
  </a>
  <a href="{{ route('client.referral.index') }}" class="nav-item {{ request()->routeIs('client.referral*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    Parceiros
    <span class="pill pill-safe" style="font-size:9px;margin-left:auto">30%</span>
  </a>
  <div class="divider"></div>
  <div class="sidebar-user-info" style="padding:8px 12px">
    <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
    <div>
      <div class="sidebar-username">{{ auth()->user()->name }}</div>
      <div class="sidebar-plan">Plano SecRadar</div>
    </div>
  </div>
  <form method="POST" action="{{ route('logout') }}" style="padding:0 12px 8px">
    @csrf
    <button type="submit" class="btn btn-outline btn-sm btn-full">Sair</button>
  </form>
</div>

  {{-- MAIN --}}
  <main class="main-content">

    {{-- Flash messages --}}
    @if(session('success'))
      <div class="flash flash-success">✔ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="flash flash-error">⚠ {{ session('error') }}</div>
    @endif
    @if(session('info'))
      <div class="flash flash-info">ℹ {{ session('info') }}</div>
    @endif

    @yield('content')
  </main>
</div>

{{-- LOCK WIDGET (substituição do ícone de bandeja) --}}
@php
  $widgetStatus = 'safe';
  $widgetMsg    = 'Seus dados estão protegidos';
  $widgetAlerts = auth()->user()->unreadAlertsCount();
  if($widgetAlerts > 0) { $widgetStatus = 'alert'; $widgetMsg = "{$widgetAlerts} alerta(s) não lido(s)"; }
@endphp
<a href="{{ route('client.alerts.index') }}" class="lock-widget {{ $widgetStatus }}" id="lock-widget" title="{{ $widgetMsg }}" style="position:fixed;bottom:20px;right:20px">
  @if($widgetStatus === 'safe')
    <svg viewBox="0 0 24 24" fill="none" stroke="var(--safe)" stroke-width="2"><rect x="5" y="11" width="14" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
  @else
    <svg viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
  @endif
  <span class="lock-dot {{ $widgetStatus }}"></span>
  <span class="lock-tooltip">{{ $widgetMsg }}</span>
</a>

<script>
// Mobile menu toggle
const toggle = document.getElementById('mob-toggle');
const mobNav = document.getElementById('mob-nav');
toggle?.addEventListener('click', () => mobNav.classList.toggle('open'));
document.querySelectorAll('.mob-nav .nav-item').forEach(l => l.addEventListener('click', () => mobNav.classList.remove('open')));

// Show sidebar user on desktop
if(window.innerWidth >= 768) {
  document.getElementById('sidebar-user-desktop').style.display = 'block';
}

// Register PWA Service Worker
if('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(()=>{});
}

// Push notification permission
function askNotificationPermission() {
  if('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
}
setTimeout(askNotificationPermission, 3000);

// Auto-refresh unread count every 60s
setInterval(() => {
  fetch('{{ route("client.scan.status") }}')
    .then(r => r.json())
    .then(d => {
      // Atualiza badges se houver mudança
      if(d.total_results !== undefined) {}
    }).catch(()=>{});
}, 60000);
</script>
@stack('scripts')
</body>
</html>
