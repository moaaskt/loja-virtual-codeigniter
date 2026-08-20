<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h3 fw-bold mb-1">E-mails & Notificações</h1>
    <p class="text-muted mb-0">Gerencie e pré-visualize os templates de e-mail transacionais</p>
  </div>
</div>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">

  <!-- Templates -->
  <div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-envelope-fill me-2 text-primary"></i>Templates Transacionais</h5>
      </div>
      <div class="card-body p-0">
        <?php foreach ($templates as $tpl): ?>
        <div class="d-flex align-items-start gap-3 p-4 border-bottom">
          <div style="width:48px;height:48px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
            <?= $tpl['icon'] ?>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1">
              <strong><?= esc($tpl['label']) ?></strong>
              <span class="badge bg-secondary-subtle text-secondary" style="font-size:10px;">
                <?= esc($tpl['gatilho']) ?>
              </span>
            </div>
            <p class="text-muted small mb-0"><?= esc($tpl['desc']) ?></p>
          </div>
          <div class="ms-2">
            <a href="<?= site_url('admin/emails/preview/' . $tpl['id']) ?>"
               target="_blank"
               class="btn btn-sm btn-outline-primary"
               title="Pré-visualizar">
              <i class="bi bi-eye"></i> Preview
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Painel de Teste SMTP -->
  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-auto">
      <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-gear-fill me-2 text-warning"></i>Testar Configuração SMTP</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-3">
          Envie um e-mail de teste para verificar se as configurações SMTP estão funcionando corretamente.
        </p>
        <form action="<?= site_url('admin/emails/testar') ?>" method="POST" id="form-smtp-test">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label for="destinatario" class="form-label fw-medium">E-mail Destinatário</label>
            <input type="email"
                   class="form-control"
                   id="destinatario"
                   name="destinatario"
                   placeholder="seu@email.com"
                   required>
            <div class="form-text">O e-mail de teste será enviado para este endereço.</div>
          </div>
          <button type="submit" class="btn btn-warning w-100" id="btn-smtp-send">
            <i class="bi bi-send-fill me-2"></i>Enviar E-mail de Teste
          </button>
        </form>
      </div>
    </div>


  </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('form-smtp-test')?.addEventListener('submit', function() {
  const btn = document.getElementById('btn-smtp-send');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
});
</script>
<?= $this->endSection() ?>
