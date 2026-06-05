@extends('layouts.app')
@section('title', 'Parceiro — Aguardando Aprovação')

@section('content')
<div style="max-width:500px;margin:80px auto;text-align:center">
  <div class="card" style="border-color:rgba(255,165,2,.3);padding:48px">
    <div style="font-size:56px;margin-bottom:16px">⏳</div>
    <div style="font-family:var(--font-brand);font-size:24px;font-weight:700;color:var(--warn);margin-bottom:8px">Solicitação em análise</div>
    <p class="text-muted" style="font-size:14px;line-height:1.7;margin-bottom:24px">
      Recebemos sua solicitação para ser parceiro SecRadar. Nossa equipe irá analisá-la em até <strong style="color:#fff">24 horas</strong> e você receberá uma notificação por e-mail.
    </p>
    <a href="{{ route('client.dashboard') }}" class="btn btn-outline btn-full">Voltar ao painel</a>
  </div>
</div>
@endsection
