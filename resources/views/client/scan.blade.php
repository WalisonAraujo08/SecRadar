@extends('layouts.app')
@section('title', 'Varredura')

@section('content')
<div class="page-header">
  <h1 class="page-title">Varredura de Dados</h1>
  <p class="page-sub">Verifique agora se seus dados aparecem em bases comprometidas.</p>
</div>

{{-- SCAN ACTION CARD --}}
<div class="card mb-6" style="border-color:rgba(0,212,170,.25)">
  <div class="flex items-center justify-between" style="flex-wrap:wrap;gap:16px">
    <div>
      <div style="font-family:var(--font-brand);font-size:16px;font-weight:700;color:var(--white);margin-bottom:4px">
        Varredura manual
      </div>
      <p class="text-muted" style="font-size:13px">
        Nosso sistema também varre automaticamente em tempo real. Você pode forçar uma verificação a qualquer momento.
      </p>
    </div>
    <form method="POST" action="{{ route('client.scan.start') }}" id="scan-form">
      @csrf
      <button type="submit" class="btn btn-primary" id="scan-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        Iniciar varredura
      </button>
    </form>
  </div>
  <div class="scan-progress mt-4" id="scan-progress" style="display:none">
    <div class="scan-progress-fill" id="scan-bar"></div>
  </div>
  <div id="scan-msg" style="font-size:12px;color:var(--accent);margin-top:6px;display:none"></div>
</div>

{{-- MONITORED EMAILS STATUS --}}
<div class="card mb-6">
  <div class="card-title">E-mails em monitoramento</div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>E-mail</th>
          <th>Última varredura</th>
          <th>Resultados</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($user->monitoredEmails as $em)
          <tr>
            <td>
              {{ $em->email }}
              @if($em->is_primary)<span class="pill pill-info" style="margin-left:6px">principal</span>@endif
            </td>
            <td class="text-muted">{{ $em->last_scanned_at?->diffForHumans() ?? 'Nunca' }}</td>
            <td>{{ $em->scanResults->count() }} vazamento(s)</td>
            <td>
              @if($em->status === 'active')
                <span class="chip"><span class="chip-dot" style="background:var(--safe)"></span>Ativo</span>
              @else
                <span class="chip"><span class="chip-dot" style="background:var(--muted)"></span>Pausado</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- RESULTS --}}
<div class="card">
  <div class="flex items-center justify-between mb-4">
    <div class="card-title" style="margin-bottom:0">Histórico de ocorrências</div>
    <span class="text-muted" style="font-size:12px">{{ $results->total() }} registro(s)</span>
  </div>

  @if($results->isEmpty())
    <div class="empty-state">
      <div class="empty-state-icon">🛡</div>
      <div class="empty-state-title">Nenhuma ocorrência</div>
      <div class="empty-state-sub">Seus dados estão limpos em todas as bases consultadas.</div>
    </div>
  @else
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Ocorrência</th>
            <th>E-mail</th>
            <th>Dados expostos</th>
            <th>Severidade</th>
            <th>Detectado</th>
          </tr>
        </thead>
        <tbody>
          @foreach($results as $r)
            <tr>
              <td style="font-weight:500">{{ $r->breach_name }}</td>
              <td class="text-muted">{{ $r->monitoredEmail?->email }}</td>
              <td>
                @foreach($r->data_exposed as $de)
                  <span class="pill pill-medium" style="margin:1px">{{ $de }}</span>
                @endforeach
              </td>
              <td><span class="pill pill-{{ $r->severity }}">{{ $r->severityLabel() }}</span></td>
              <td class="text-muted" style="white-space:nowrap">{{ $r->detected_at->format('d/m/Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div style="margin-top:16px">{{ $results->links() }}</div>
  @endif
</div>

@push('scripts')
<script>
const form = document.getElementById('scan-form');
const btn  = document.getElementById('scan-btn');
const bar  = document.getElementById('scan-bar');
const prog = document.getElementById('scan-progress');
const msg  = document.getElementById('scan-msg');

const sources = [
  'Verificando base A…','Verificando base B…','Verificando base C…',
  'Consultando registros de segurança…','Cruzando dados…',
  'Analisando ocorrências…','Finalizando varredura…'
];

let polling = null;

form.addEventListener('submit', () => {
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Varrendo…';
  prog.style.display = 'block';
  msg.style.display  = 'block';

  // Animação visual
  let step = 0;
  const iv = setInterval(() => {
    if (step >= sources.length) { clearInterval(iv); return; }
    msg.textContent = sources[step];
    bar.style.width = ((step + 1) / sources.length * 100) + '%';
    step++;
  }, 500);

  // Polling — verifica resultado a cada 3s
  setTimeout(() => {
    polling = setInterval(() => {
      fetch('{{ route("client.scan.status") }}')
        .then(r => r.json())
        .then(d => {
          if (!d.in_progress) {
            clearInterval(polling);
            clearInterval(iv);
            bar.style.width = '100%';
            msg.textContent = '✔ Varredura concluída! Atualizando resultados...';
            msg.style.color = 'var(--safe)';
            setTimeout(() => location.reload(), 1500);
          }
        })
        .catch(() => clearInterval(polling));
    }, 3000);
  }, 2000);
});
</script>
@endpush
@endsection
