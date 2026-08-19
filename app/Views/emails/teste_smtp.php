<?= $this->extend('emails/layouts/email_base') ?>
<?php $emailTitle = "Teste SMTP — G'Store" ?>

<?= $this->section('email_content') ?>

<!-- HERO -->
<div class="email-hero">
  <span class="status-badge badge-success">🔧 Teste de Configuração</span>
  <h1>SMTP configurado com sucesso!</h1>
  <p>
    Este e-mail confirma que as configurações de SMTP estão funcionando corretamente.<br>
    Enviado para: <strong><?= esc($destinatario ?? '') ?></strong>
  </p>
</div>

<!-- BODY -->
<div class="email-body">

  <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:12px;padding:24px;text-align:center;margin-bottom:24px;">
    <div style="font-size:56px;margin-bottom:12px;">🎉</div>
    <div style="font-size:20px;font-weight:700;color:#16a34a;margin-bottom:8px;">Tudo certo!</div>
    <div style="font-size:14px;color:#15803d;">
      Seu servidor SMTP está respondendo corretamente.<br>
      As notificações transacionais estão prontas para uso.
    </div>
  </div>

  <div class="info-box">
    <p class="info-box-title">📊 Detalhes do Teste</p>
    <div class="info-row">
      <span class="info-label">Destinatário</span>
      <span class="info-value"><?= esc($destinatario ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Data / Hora</span>
      <span class="info-value"><?= esc($timestamp ?? date('d/m/Y H:i:s')) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Status</span>
      <span class="info-value green" style="font-weight:700;">✅ Entregue</span>
    </div>
  </div>

  <p style="font-size:13px;color:#94a3b8;text-align:center;margin:24px 0 0;">
    G'Store — Sistema de Notificações por E-mail
  </p>

</div><!-- /email-body -->

<?= $this->endSection() ?>
