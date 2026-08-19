<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="fs-4 fw-bold mb-0">
        <i class="bi bi-bag-fill text-primary me-2"></i><?= esc($title) ?>
    </h1>
    <a href="<?= site_url('/') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btn-continuar-comprando">
        <i class="bi bi-arrow-left me-1"></i> Continuar comprando
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (empty($carrinho)): ?>
    <!-- Empty cart state -->
    <div class="text-center py-5 my-4">
        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle"
            style="width:96px;height:96px;background:#f1f5f9;">
            <i class="bi bi-bag-x" style="font-size:2.5rem;color:#94a3b8;"></i>
        </div>
        <h2 class="fs-5 fw-bold mb-2">Seu carrinho está vazio</h2>
        <p class="text-muted mb-4">Adicione produtos incríveis para continuar.</p>
        <a href="<?= site_url('/') ?>" class="btn btn-primary rounded-pill px-4" id="btn-ir-loja">
            <i class="bi bi-shop me-2"></i>Explorar produtos
        </a>
    </div>

<?php else: ?>

    <?php
        $subtotal   = $totais['subtotal'] ?? 0;
        $desconto   = $totais['desconto'] ?? 0;
        $freteValor = $totais['frete'] ?? 0;
        $totalFinal = $totais['total'] ?? $subtotal;
        $cupomAtivo = $totais['cupom'] ?? null;
        $freteAtivo = $totais['frete_info'] ?? null;
    ?>

    <div class="row g-4">

        <!-- ===== CART ITEMS & ACTIONS ===== -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table cart-table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:80px;" class="ps-3">Produto</th>
                                    <th></th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th class="pe-3" style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrinho as $id => $item): ?>
                                    <?php $itemSubtotal = $item['preco'] * $item['quantidade']; ?>
                                    <tr>
                                        <td class="ps-3">
                                            <img src="<?= strpos($item['imagem'], 'http') === 0
                                                ? esc($item['imagem'])
                                                : base_url('uploads/produtos/' . esc($item['imagem'])) ?>"
                                                alt="<?= esc($item['nome']) ?>"
                                                class="img-thumb-table rounded-3" style="width:56px;height:56px;object-fit:cover;">
                                        </td>
                                        <td>
                                            <span class="fw-semibold d-block"><?= esc($item['nome']) ?></span>
                                            <?php if (!empty($item['tamanho']) || !empty($item['cor'])): ?>
                                                <small class="text-muted d-block mt-1">
                                                    <?= !empty($item['tamanho']) ? 'Tamanho: ' . esc($item['tamanho']) : '' ?>
                                                    <?= !empty($item['tamanho']) && !empty($item['cor']) ? ' | ' : '' ?>
                                                    <?= !empty($item['cor']) ? 'Cor: ' . esc($item['cor']) : '' ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted">
                                            R$ <?= esc(number_format($item['preco'], 2, ',', '.')) ?>
                                        </td>
                                        <td>
                                            <?= form_open('carrinho/atualizar', ['class' => 'd-flex align-items-center gap-2']) ?>
                                                <input type="hidden" name="cart_key" value="<?= $id ?>">
                                                <input type="number" name="quantidade"
                                                    class="form-control form-control-sm text-center"
                                                    value="<?= esc($item['quantidade']) ?>"
                                                    min="1"
                                                    style="width:64px; border-radius:8px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary"
                                                    style="border-radius:8px;"
                                                    title="Atualizar quantidade"
                                                    id="btn-atualizar-<?= $id ?>">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            <?= form_close() ?>
                                        </td>
                                        <td class="fw-semibold">
                                            R$ <?= esc(number_format($itemSubtotal, 2, ',', '.')) ?>
                                        </td>
                                        <td class="pe-3">
                                            <?= form_open('carrinho/remover/' . $id, ['class' => 'd-inline']) ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    style="border-radius:8px;"
                                                    title="Remover do carrinho"
                                                    id="btn-remover-<?= $id ?>">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            <?= form_close() ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===== CUPOM & FRETE BOXES ===== -->
            <div class="row g-3">

                <!-- Box Cupom de Desconto -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-ticket-perforated-fill text-primary me-2"></i>Cupom de Desconto
                        </h6>

                        <?php if ($cupomAtivo): ?>
                            <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-0 rounded-3">
                                <div>
                                    <span class="badge bg-success font-monospace fs-6 me-2"><?= esc($cupomAtivo['codigo']) ?></span>
                                    <span class="fw-semibold small text-success">
                                        - R$ <?= esc(number_format($desconto, 2, ',', '.')) ?>
                                    </span>
                                </div>
                                <?= form_open('carrinho/remover-cupom', ['class' => 'd-inline', 'id' => 'form-remover-cupom']) ?>
                                    <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none p-0" title="Remover cupom">
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                <?= form_close() ?>
                            </div>
                        <?php else: ?>
                            <?= form_open('carrinho/aplicar-cupom', ['id' => 'form-aplicar-cupom']) ?>
                                <div class="input-group">
                                    <input type="text" name="codigo" id="input-cupom" class="form-control rounded-start-pill text-uppercase font-monospace"
                                        placeholder="Código do cupom" required maxlength="50">
                                    <button class="btn btn-primary rounded-end-pill px-3 fw-semibold" type="submit" id="btn-aplicar-cupom">
                                        Aplicar
                                    </button>
                                </div>
                            <?= form_close() ?>
                            <div id="cupom-feedback" class="mt-2 small d-none"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Box Cálculo de Frete -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-truck text-primary me-2"></i>Calcular Frete
                            </h6>
                            <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank" class="text-muted small text-decoration-none">
                                Não sei meu CEP
                            </a>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" id="input-frete-cep" class="form-control rounded-start-pill font-monospace"
                                placeholder="00000-000" maxlength="9" value="<?= esc($freteAtivo['cep'] ?? '') ?>">
                            <button class="btn btn-outline-primary rounded-end-pill px-3 fw-semibold" type="button" id="btn-calcular-frete-cart">
                                Calcular
                            </button>
                        </div>
                        <div id="frete-opcoes-cart" class="mt-2">
                            <?php if ($freteAtivo): ?>
                                <div class="alert alert-light border d-flex align-items-center justify-content-between p-2 mb-0 rounded-3">
                                    <div>
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        <span class="fw-semibold small"><?= esc($freteAtivo['modalidade']) ?></span>
                                        <small class="text-muted d-block" style="font-size:0.75rem;">Prazo: <?= esc($freteAtivo['prazo']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-success small">
                                            <?= (float)$freteAtivo['valor'] === 0.0 ? 'GRÁTIS' : 'R$ ' . number_format($freteAtivo['valor'], 2, ',', '.') ?>
                                        </strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===== ORDER SUMMARY ===== -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:80px;">
                <div class="card-body p-4">
                    <h2 class="fs-6 fw-bold mb-3 text-muted text-uppercase"
                        style="letter-spacing:.07em; font-size:.75rem !important;">
                        Resumo do Pedido
                    </h2>

                    <!-- Subtotal -->
                    <div class="d-flex justify-content-between mb-2 fs-6">
                        <span class="text-muted">Subtotal</span>
                        <span id="resumo-subtotal">R$ <?= esc(number_format($subtotal, 2, ',', '.')) ?></span>
                    </div>

                    <!-- Desconto do Cupom -->
                    <?php if ($desconto > 0): ?>
                        <div class="d-flex justify-content-between mb-2 fs-6 text-success" id="resumo-desconto-row">
                            <span><i class="bi bi-tag-fill me-1"></i>Desconto (<?= esc($cupomAtivo['codigo'] ?? '') ?>)</span>
                            <span id="resumo-desconto">- R$ <?= esc(number_format($desconto, 2, ',', '.')) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Frete -->
                    <div class="d-flex justify-content-between mb-3 fs-6">
                        <span class="text-muted">Frete</span>
                        <span id="resumo-frete" class="<?= $freteAtivo ? 'text-success fw-semibold' : 'text-muted' ?>">
                            <?php if ($freteAtivo): ?>
                                <?= (float)$freteAtivo['valor'] === 0.0 ? 'GRÁTIS' : 'R$ ' . number_format($freteAtivo['valor'], 2, ',', '.') ?>
                            <?php else: ?>
                                A calcular
                            <?php endif; ?>
                        </span>
                    </div>

                    <hr>

                    <!-- Total Final -->
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-5">Total</strong>
                        <strong class="fs-4 text-success" id="resumo-total">
                            R$ <?= esc(number_format($totalFinal, 2, ',', '.')) ?>
                        </strong>
                    </div>

                    <!-- Trigger checkout offcanvas -->
                    <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm"
                        style="border-radius:14px; font-size:1.05rem;"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasCheckout"
                        aria-controls="offcanvasCheckout"
                        id="btn-finalizar">
                        <i class="bi bi-shield-lock-fill me-2"></i>
                        Finalizar Compra
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- ===== CHECKOUT OFFCANVAS ===== -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCheckout"
        aria-labelledby="offcanvasCheckoutLabel" style="width:min(520px, 100vw);">
        <div class="offcanvas-header border-bottom">
            <div>
                <h2 class="offcanvas-title fw-bold fs-5" id="offcanvasCheckoutLabel">
                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>Endereço de Entrega
                </h2>
                <small class="text-muted">Preencha para finalizar seu pedido</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body">
            <?= form_open('checkout/finalizar', ['id' => 'form-checkout']) ?>
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-5">
                    <div class="form-floating">
                        <input type="text" id="cep" name="cep" class="form-control"
                            placeholder="00000-000" maxlength="9"
                            value="<?= esc($freteAtivo['cep'] ?? '') ?>" required>
                        <label for="cep">CEP <span class="text-danger">*</span></label>
                    </div>
                    <div id="cep-feedback" class="form-text text-danger d-none mt-1">
                        <i class="bi bi-exclamation-circle me-1"></i>CEP não encontrado.
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="form-floating">
                        <input type="text" id="logradouro" name="logradouro" class="form-control"
                            placeholder="Rua / Av." required>
                        <label for="logradouro">Logradouro <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-4">
                    <div class="form-floating">
                        <input type="text" id="numero" name="numero" class="form-control"
                            placeholder="Nº" required>
                        <label for="numero">Número <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-8">
                    <div class="form-floating">
                        <input type="text" id="complemento" name="complemento" class="form-control"
                            placeholder="Apto, bloco...">
                        <label for="complemento">Complemento</label>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-floating">
                        <input type="text" id="bairro" name="bairro" class="form-control"
                            placeholder="Bairro" required>
                        <label for="bairro">Bairro <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-floating">
                        <input type="text" id="cidade" name="cidade" class="form-control"
                            placeholder="Cidade" required>
                        <label for="cidade">Cidade <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-floating">
                        <input type="text" id="uf" name="uf" class="form-control"
                            placeholder="UF" maxlength="2" required>
                        <label for="uf">UF <span class="text-danger">*</span></label>
                    </div>
                </div>
            </div>

            <!-- Resumo Financeiro no Checkout -->
            <div class="card bg-light border-0 rounded-3 p-3 mt-4">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Subtotal</span>
                    <span>R$ <?= esc(number_format($subtotal, 2, ',', '.')) ?></span>
                </div>
                <?php if ($desconto > 0): ?>
                    <div class="d-flex justify-content-between small text-success mb-1">
                        <span>Desconto (<?= esc($cupomAtivo['codigo'] ?? '') ?>)</span>
                        <span>- R$ <?= esc(number_format($desconto, 2, ',', '.')) ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Frete <?= $freteAtivo ? '(' . esc($freteAtivo['modalidade']) . ')' : '' ?></span>
                    <span class="text-success fw-semibold">
                        <?= $freteAtivo ? ((float)$freteAtivo['valor'] === 0.0 ? 'GRÁTIS' : 'R$ ' . number_format($freteAtivo['valor'], 2, ',', '.')) : 'A calcular' ?>
                    </span>
                </div>
                <div class="border-top pt-2 d-flex justify-content-between fw-bold fs-5 text-dark">
                    <span>Total do Pedido</span>
                    <span class="text-success">R$ <?= esc(number_format($totalFinal, 2, ',', '.')) ?></span>
                </div>
            </div>

            <div class="border-top mt-4 pt-4">
                <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm"
                    style="border-radius:12px; font-size:1.05rem;" id="btn-confirmar-pedido">
                    <i class="bi bi-bag-check-fill me-2"></i>
                    Confirmar Pedido
                </button>
            </div>

            <?= form_close() ?>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------------------
    // Consulta de Endereço por CEP no Checkout Offcanvas
    // ----------------------------------------------------------------
    const cepInput = document.getElementById('cep');
    const feedback = document.getElementById('cep-feedback');

    async function buscarEnderecoPorCep(cepVal) {
        const cep = (cepVal || '').replace(/\D/g, '');
        if (cep.length !== 8) return;

        if (feedback) feedback.classList.add('d-none');

        try {
            const res  = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await res.json();

            if (data.erro) {
                if (feedback) feedback.classList.remove('d-none');
                return;
            }

            const log = document.getElementById('logradouro');
            const bai = document.getElementById('bairro');
            const cid = document.getElementById('cidade');
            const uf  = document.getElementById('uf');
            const num = document.getElementById('numero');

            if (log) log.value = data.logradouro || '';
            if (bai) bai.value = data.bairro     || '';
            if (cid) cid.value = data.localidade  || '';
            if (uf)  uf.value  = data.uf          || '';
            if (num) num.focus();
        } catch (e) {
            if (feedback) feedback.classList.remove('d-none');
        }
    }

    if (cepInput) {
        cepInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
        });

        cepInput.addEventListener('blur', function () {
            buscarEnderecoPorCep(this.value);
        });

        // Se já tiver CEP inicial vindo do frete selecionado
        if (cepInput.value.replace(/\D/g, '').length === 8) {
            buscarEnderecoPorCep(cepInput.value);
        }
    }

    // ----------------------------------------------------------------
    // Cálculo de Frete no Carrinho
    // ----------------------------------------------------------------
    const inputFreteCart = document.getElementById('input-frete-cep');
    const btnCalcularCart = document.getElementById('btn-calcular-frete-cart');
    const freteOpcoesCart = document.getElementById('frete-opcoes-cart');

    if (inputFreteCart && btnCalcularCart && freteOpcoesCart) {
        inputFreteCart.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
        });

        inputFreteCart.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') btnCalcularCart.click();
        });

        btnCalcularCart.addEventListener('click', async function () {
            const cep = inputFreteCart.value.replace(/\D/g, '');
            if (cep.length !== 8) {
                freteOpcoesCart.innerHTML = `
                    <div class="alert alert-warning py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>Digite um CEP com 8 dígitos.
                    </div>`;
                return;
            }

            freteOpcoesCart.innerHTML = `
                <div class="text-center py-3 text-muted small">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Calculando frete...
                </div>`;

            try {
                const formData = new FormData();
                formData.append('cep', cep);

                const response = await fetch('<?= site_url('api/frete/calcular') ?>', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (!data.ok) {
                    freteOpcoesCart.innerHTML = `
                        <div class="alert alert-danger py-2 px-3 small mb-0 rounded-3">
                            <i class="bi bi-exclamation-circle me-1"></i>${data.erro || 'Erro ao calcular frete.'}
                        </div>`;
                    return;
                }

                let html = `<div class="list-group list-group-flush rounded-3 border">`;
                data.opcoes.forEach(op => {
                    const precoFmt = op.valor === 0
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">GRÁTIS</span>`
                        : `<strong class="text-success small">R$ ${op.valor.toFixed(2).replace('.', ',')}</strong>`;

                    html += `
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 btn-select-frete"
                            data-modalidade="${op.nome}" data-valor="${op.valor}" data-prazo="${op.prazo}" data-cep="${cep}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi ${op.icone} text-primary"></i>
                                <div>
                                    <div class="fw-semibold small">${op.nome}</div>
                                    <small class="text-muted" style="font-size:0.75rem;">${op.prazo}</small>
                                </div>
                            </div>
                            <div>${precoFmt}</div>
                        </button>`;
                });
                html += `</div>`;
                freteOpcoesCart.innerHTML = html;

                // Anexa listeners nos botões de seleção de frete
                document.querySelectorAll('.btn-select-frete').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const modalidade = this.dataset.modalidade;
                        const valor      = this.dataset.valor;
                        const prazo      = this.dataset.prazo;
                        const cep        = this.dataset.cep;

                        fetch('<?= site_url('carrinho/selecionar-frete') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ modalidade, valor, prazo, cep })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success || data.ok) {
                                window.location.reload();
                            }
                        })
                        .catch(err => {
                            console.error('Erro ao selecionar frete:', err);
                        });
                    });
                });

            } catch (err) {
                console.error(err);
                freteOpcoesCart.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-exclamation-circle me-1"></i>Erro ao calcular frete. Tente novamente.
                    </div>`;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
