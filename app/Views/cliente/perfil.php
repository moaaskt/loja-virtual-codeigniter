<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minha Conta / Dados & Segurança</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-12 col-lg-4 col-xl-3">
            <?= $this->include('cliente/_sidebar') ?>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-12 col-lg-8 col-xl-9">

            <div class="mb-4">
                <h1 class="fs-4 fw-bold mb-1 text-dark">
                    <i class="bi bi-person-gear text-primary me-2"></i>Dados & Segurança da Conta
                </h1>
                <p class="text-muted small mb-0">Atualize suas informações pessoais e gerencie sua senha de acesso.</p>
            </div>

            <!-- Feedback Messages de Dados -->
            <?php if (session()->getFlashdata('sucesso')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('sucesso') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('erro') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Card 1: Dados Pessoais -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-light p-3 border-bottom">
                    <h2 class="fs-6 fw-bold mb-0 text-dark">
                        <i class="bi bi-person-vcard text-primary me-2"></i>Informações Pessoais
                    </h2>
                </div>
                <div class="card-body p-4 bg-white">
                    <?= form_open('minha-conta/perfil/salvar', ['id' => 'form-perfil']) ?>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="nome" class="form-label small fw-bold text-muted">Nome Completo *</label>
                                <input type="text" name="nome" id="nome" class="form-control rounded-3"
                                       value="<?= esc($usuario['nome']) ?>" required minlength="3" maxlength="128">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label small fw-bold text-muted">E-mail de Cadastro</label>
                                <input type="email" id="email" class="form-control rounded-3 bg-light font-monospace text-muted"
                                       value="<?= esc($usuario['email']) ?>" readonly>
                                <small class="text-muted" style="font-size:0.75rem;">Para alterar o e-mail, contate o suporte.</small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">Membro desde</label>
                                <input type="text" class="form-control rounded-3 bg-light text-muted"
                                       value="<?= !empty($usuario['criado_em']) ? date('d/m/Y', strtotime($usuario['criado_em'])) : 'Data não informada' ?>" readonly>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-check-lg me-1"></i>Salvar Alterações
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Feedback Messages de Senha -->
            <?php if (session()->getFlashdata('sucesso_senha')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-shield-check me-2"></i><?= session()->getFlashdata('sucesso_senha') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('erro_senha')): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-shield-exclamation me-2"></i><?= session()->getFlashdata('erro_senha') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Card 2: Alteração de Senha -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-light p-3 border-bottom">
                    <h2 class="fs-6 fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-lock text-primary me-2"></i>Alterar Senha de Acesso
                    </h2>
                </div>
                <div class="card-body p-4 bg-white">
                    <?= form_open('minha-conta/perfil/trocar-senha', ['id' => 'form-senha']) ?>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="senha_atual" class="form-label small fw-bold text-muted">Senha Atual *</label>
                                <input type="password" name="senha_atual" id="senha_atual" class="form-control rounded-3"
                                       placeholder="••••••••" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="nova_senha" class="form-label small fw-bold text-muted">Nova Senha *</label>
                                <input type="password" name="nova_senha" id="nova_senha" class="form-control rounded-3"
                                       placeholder="Mínimo 6 caracteres" required minlength="6">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="confirmar_senha" class="form-label small fw-bold text-muted">Confirmar Nova Senha *</label>
                                <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control rounded-3"
                                       placeholder="Repita a nova senha" required minlength="6">
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-outline-primary rounded-pill px-4 fw-semibold shadow-xs">
                                <i class="bi bi-key-fill me-1"></i>Atualizar Senha
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
