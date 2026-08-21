<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minha Conta / Endereços</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-12 col-lg-4 col-xl-3">
            <?= $this->include('cliente/_sidebar') ?>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-12 col-lg-8 col-xl-9">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h1 class="fs-4 fw-bold mb-1 text-dark">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>Endereços de Entrega
                    </h1>
                    <p class="text-muted small mb-0">Gerencie seus endereços para compras rápidas no checkout.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalEndereco" onclick="prepararNovoEndereco()">
                    <i class="bi bi-plus-lg me-1"></i>Novo Endereço
                </button>
            </div>

            <!-- Feedback Messages -->
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

            <!-- Grid de Endereços -->
            <?php if (!empty($enderecos)): ?>
                <div class="row g-3" id="lista-enderecos">
                    <?php foreach ($enderecos as $end): ?>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white position-relative <?= !empty($end['padrao']) ? 'border border-2 border-primary' : '' ?>">
                                
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h2 class="fs-6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                        <i class="bi <?= !empty($end['padrao']) ? 'bi-star-fill text-warning' : 'bi-geo-alt text-primary' ?>"></i>
                                        <?= esc($end['titulo']) ?>
                                    </h2>
                                    <?php if (!empty($end['padrao'])): ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                            Padrão
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-secondary small lh-lg flex-grow-1">
                                    <?php if (!empty($end['destinatario'])): ?>
                                        <p class="mb-1 text-dark fw-semibold">
                                            <i class="bi bi-person me-1"></i><?= esc($end['destinatario']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="mb-1">
                                        <?= esc($end['logradouro']) ?>, <?= esc($end['numero']) ?>
                                        <?= !empty($end['complemento']) ? ' - ' . esc($end['complemento']) : '' ?>
                                    </p>
                                    <p class="mb-1"><?= esc($end['bairro']) ?> — <?= esc($end['cidade']) ?>/<?= esc($end['uf']) ?></p>
                                    <p class="mb-2 font-monospace text-muted">CEP: <?= esc($end['cep']) ?></p>
                                </div>

                                <!-- Ações do Endereço -->
                                <div class="pt-3 border-top mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <?php if (empty($end['padrao'])): ?>
                                            <?= form_open('minha-conta/enderecos/padrao/' . $end['id'], ['class' => 'd-inline']) ?>
                                                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="font-size:0.75rem;">
                                                    <i class="bi bi-star me-1"></i>Tornar Padrão
                                                </button>
                                            <?= form_close() ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-light btn-sm text-secondary rounded-pill px-3"
                                                onclick='editarEndereco(<?= json_encode($end, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                                title="Editar Endereço">
                                            <i class="bi bi-pencil me-1"></i>Editar
                                        </button>

                                        <?= form_open('minha-conta/enderecos/excluir/' . $end['id'], ['class' => 'd-inline', 'onsubmit' => 'return confirm("Deseja realmente remover este endereço?");']) ?>
                                            <button type="submit" class="btn btn-light btn-sm text-danger rounded-circle p-2" title="Excluir Endereço">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?= form_close() ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="card border-0 bg-light p-5 text-center rounded-4 shadow-sm">
                    <div class="py-4">
                        <i class="bi bi-geo-alt text-muted mb-3 d-block" style="font-size: 3.5rem;"></i>
                        <h3 class="fs-5 fw-bold text-dark mb-2">Nenhum endereço cadastrado</h3>
                        <p class="text-muted small mb-4">Adicione seus endereços de entrega para agilizar suas compras.</p>
                        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalEndereco" onclick="prepararNovoEndereco()">
                            <i class="bi bi-plus-lg me-1"></i>Cadastrar Primeiro Endereço
                        </button>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ===== MODAL ADICIONAR / EDITAR ENDEREÇO COM VIACEP ===== -->
<div class="modal fade" id="modalEndereco" tabindex="-1" aria-labelledby="modalEnderecoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-primary text-white p-4 border-0">
                <h5 class="modal-title fw-bold" id="modalEnderecoLabel">
                    <i class="bi bi-geo-alt-fill me-2"></i>Cadastrar Endereço
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <?= form_open('minha-conta/enderecos/salvar', ['id' => 'form-endereco']) ?>
                <input type="hidden" name="endereco_id" id="end-id" value="">

                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <!-- Título do Endereço -->
                        <div class="col-12 col-md-6">
                            <label for="end-titulo" class="form-label small fw-bold text-muted">Identificação do Endereço *</label>
                            <input type="text" name="titulo" id="end-titulo" class="form-control rounded-3"
                                   placeholder="Ex: Minha Casa, Trabalho, Apartamento" required maxlength="60">
                        </div>

                        <!-- Destinatário -->
                        <div class="col-12 col-md-6">
                            <label for="end-destinatario" class="form-label small fw-bold text-muted">Nome de Quem Recebe</label>
                            <input type="text" name="destinatario" id="end-destinatario" class="form-control rounded-3"
                                   placeholder="Ex: João da Silva (Opcional)" maxlength="128">
                        </div>

                        <!-- CEP com busca automática -->
                        <div class="col-12 col-md-4">
                            <label for="end-cep" class="form-label small fw-bold text-muted">CEP *</label>
                            <div class="input-group">
                                <input type="text" name="cep" id="end-cep" class="form-control rounded-start-3 font-monospace"
                                       placeholder="00000-000" maxlength="9" required>
                                <button class="btn btn-outline-primary" type="button" id="btn-buscar-viacep" title="Buscar CEP">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="cep-feedback" style="font-size:0.75rem;">Digite 8 dígitos para autopreencher</small>
                        </div>

                        <!-- Logradouro (Rua / Av) -->
                        <div class="col-12 col-md-8">
                            <label for="end-logradouro" class="form-label small fw-bold text-muted">Logradouro (Rua, Avenida...) *</label>
                            <input type="text" name="logradouro" id="end-logradouro" class="form-control rounded-3"
                                   placeholder="Ex: Rua das Flores" required maxlength="255">
                        </div>

                        <!-- Número -->
                        <div class="col-12 col-md-4">
                            <label for="end-numero" class="form-label small fw-bold text-muted">Número *</label>
                            <input type="text" name="numero" id="end-numero" class="form-control rounded-3"
                                   placeholder="Ex: 123" required maxlength="30">
                        </div>

                        <!-- Complemento -->
                        <div class="col-12 col-md-8">
                            <label for="end-complemento" class="form-label small fw-bold text-muted">Complemento</label>
                            <input type="text" name="complemento" id="end-complemento" class="form-control rounded-3"
                                   placeholder="Ex: Apto 42, Bloco B (Opcional)" maxlength="100">
                        </div>

                        <!-- Bairro -->
                        <div class="col-12 col-md-5">
                            <label for="end-bairro" class="form-label small fw-bold text-muted">Bairro *</label>
                            <input type="text" name="bairro" id="end-bairro" class="form-control rounded-3"
                                   placeholder="Ex: Centro" required maxlength="100">
                        </div>

                        <!-- Cidade -->
                        <div class="col-12 col-md-5">
                            <label for="end-cidade" class="form-label small fw-bold text-muted">Cidade *</label>
                            <input type="text" name="cidade" id="end-cidade" class="form-control rounded-3"
                                   placeholder="Ex: São Paulo" required maxlength="100">
                        </div>

                        <!-- UF -->
                        <div class="col-12 col-md-2">
                            <label for="end-uf" class="form-label small fw-bold text-muted">UF *</label>
                            <input type="text" name="uf" id="end-uf" class="form-control rounded-3 text-uppercase"
                                   placeholder="SP" required maxlength="2">
                        </div>

                        <!-- Flag Padrão -->
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="padrao" id="end-padrao" value="1">
                                <label class="form-check-label small fw-semibold text-dark" for="end-padrao">
                                    Definir como meu endereço padrão de entrega
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i>Salvar Endereço
                    </button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function prepararNovoEndereco() {
    document.getElementById('modalEnderecoLabel').innerHTML = '<i class="bi bi-geo-alt-fill me-2"></i>Cadastrar Endereço';
    document.getElementById('end-id').value = '';
    document.getElementById('end-titulo').value = 'Minha Casa';
    document.getElementById('end-destinatario').value = '';
    document.getElementById('end-cep').value = '';
    document.getElementById('end-logradouro').value = '';
    document.getElementById('end-numero').value = '';
    document.getElementById('end-complemento').value = '';
    document.getElementById('end-bairro').value = '';
    document.getElementById('end-cidade').value = '';
    document.getElementById('end-uf').value = '';
    document.getElementById('end-padrao').checked = false;
    document.getElementById('cep-feedback').textContent = 'Digite 8 dígitos para autopreencher';
}

function editarEndereco(dados) {
    document.getElementById('modalEnderecoLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Endereço';
    document.getElementById('end-id').value = dados.id || '';
    document.getElementById('end-titulo').value = dados.titulo || '';
    document.getElementById('end-destinatario').value = dados.destinatario || '';
    document.getElementById('end-cep').value = dados.cep || '';
    document.getElementById('end-logradouro').value = dados.logradouro || '';
    document.getElementById('end-numero').value = dados.numero || '';
    document.getElementById('end-complemento').value = dados.complemento || '';
    document.getElementById('end-bairro').value = dados.bairro || '';
    document.getElementById('end-cidade').value = dados.cidade || '';
    document.getElementById('end-uf').value = dados.uf || '';
    document.getElementById('end-padrao').checked = (dados.padrao == 1);
    
    const modalEl = document.getElementById('modalEndereco');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const cepInput = document.getElementById('end-cep');
    const btnBuscaCep = document.getElementById('btn-buscar-viacep');
    const feedback = document.getElementById('cep-feedback');

    async function buscarCep() {
        if (!cepInput) return;
        const cep = cepInput.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        if (feedback) feedback.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span> Buscando endereço...';

        try {
            const resp = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await resp.json();

            if (data.erro) {
                if (feedback) feedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>CEP não encontrado.</span>';
                return;
            }

            document.getElementById('end-logradouro').value = data.logradouro || '';
            document.getElementById('end-bairro').value     = data.bairro || '';
            document.getElementById('end-cidade').value     = data.localidade || '';
            document.getElementById('end-uf').value         = data.uf || '';
            document.getElementById('end-numero').focus();

            if (feedback) feedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Endereço preenchido automaticamente!</span>';
        } catch (e) {
            console.error(e);
            if (feedback) feedback.innerHTML = '<span class="text-danger">Erro ao consultar CEP. Preencha manualmente.</span>';
        }
    }

    if (cepInput) {
        cepInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
            if (this.value.replace(/\D/g, '').length === 8) {
                buscarCep();
            }
        });
    }

    if (btnBuscaCep) {
        btnBuscaCep.addEventListener('click', buscarCep);
    }
});
</script>
<?= $this->endSection() ?>
