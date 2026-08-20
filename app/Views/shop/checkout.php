<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
.checkout-step-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
}

.checkout-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.checkout-summary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.checkout-item-thumb {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
}

.pay-method-label {
    cursor: pointer;
    transition: all 0.2s ease;
    border-width: 2px !important;
}

.pay-method-label:hover {
    border-color: #6366f1 !important;
    background-color: #f8fafc;
}

.btn-check:checked + .pay-method-label {
    border-color: #6366f1 !important;
    background-color: #eef2ff !important;
    box-shadow: 0 0 0 1px #6366f1;
}

.checkout-sticky-summary {
    position: -webkit-sticky;
    position: sticky;
    top: 90px;
}
</style>

<!-- ===== BREADCRUMB & HEADER ===== -->
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none text-muted">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('carrinho') ?>" class="text-decoration-none text-muted">Carrinho</a></li>
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Checkout Seguro</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-1 text-success small fw-semibold">
            <i class="bi bi-shield-lock-fill"></i> Ambiente Seguro SSL 256-bit
        </div>
    </div>
    <h1 class="fs-4 fw-bold text-dark mb-0">
        <i class="bi bi-shield-check text-primary me-2"></i>Finalizar Compra
    </h1>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
    $subtotal   = $totais['subtotal'] ?? 0;
    $desconto   = $totais['desconto'] ?? 0;
    $freteValor = $totais['frete'] ?? 0;
    $totalFinal = $totais['total'] ?? $subtotal;
    $cupomAtivo = $totais['cupom'] ?? null;
    $freteAtivo = $totais['frete_info'] ?? null;
?>

<?= form_open('checkout/finalizar', ['id' => 'form-checkout']) ?>
<?= csrf_field() ?>

<div class="row g-4">

    <!-- ===== COLUNA DA ESQUERDA: FORMULÁRIOS DE ENTREGA E PAGAMENTO ===== -->
    <div class="col-lg-7">

        <!-- 1. Endereço de Entrega -->
        <div class="checkout-card p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="checkout-step-badge bg-primary text-white">1</span>
                    <h2 class="fs-5 fw-bold text-dark mb-0">Endereço de Entrega</h2>
                </div>
                <small class="text-muted">* Campos obrigatórios</small>
            </div>

            <div class="row g-3">
                <!-- CEP -->
                <div class="col-12 col-sm-5 col-md-4">
                    <div class="form-floating">
                        <input type="text" id="cep" name="cep" class="form-control font-monospace"
                            placeholder="00000-000" maxlength="9"
                            value="<?= esc(old('cep', $freteAtivo['cep'] ?? '')) ?>" required>
                        <label for="cep">CEP <span class="text-danger">*</span></label>
                    </div>
                    <div id="cep-feedback" class="form-text text-danger d-none mt-1">
                        <i class="bi bi-exclamation-circle me-1"></i>CEP não encontrado.
                    </div>
                </div>

                <!-- Logradouro -->
                <div class="col-12 col-sm-7 col-md-8">
                    <div class="form-floating">
                        <input type="text" id="logradouro" name="logradouro" class="form-control"
                            placeholder="Rua / Av." value="<?= esc(old('logradouro')) ?>" required>
                        <label for="logradouro">Logradouro <span class="text-danger">*</span></label>
                    </div>
                </div>

                <!-- Número -->
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="form-floating">
                        <input type="text" id="numero" name="numero" class="form-control"
                            placeholder="Nº" value="<?= esc(old('numero')) ?>" required>
                        <label for="numero">Número <span class="text-danger">*</span></label>
                    </div>
                </div>

                <!-- Complemento -->
                <div class="col-12 col-sm-8 col-md-8">
                    <div class="form-floating">
                        <input type="text" id="complemento" name="complemento" class="form-control"
                            placeholder="Apto, bloco..." value="<?= esc(old('complemento')) ?>">
                        <label for="complemento">Complemento (opcional)</label>
                    </div>
                </div>

                <!-- Bairro -->
                <div class="col-12 col-sm-5 col-md-5">
                    <div class="form-floating">
                        <input type="text" id="bairro" name="bairro" class="form-control"
                            placeholder="Bairro" value="<?= esc(old('bairro')) ?>" required>
                        <label for="bairro">Bairro <span class="text-danger">*</span></label>
                    </div>
                </div>

                <!-- Cidade -->
                <div class="col-8 col-sm-5 col-md-5">
                    <div class="form-floating">
                        <input type="text" id="cidade" name="cidade" class="form-control"
                            placeholder="Cidade" value="<?= esc(old('cidade')) ?>" required>
                        <label for="cidade">Cidade <span class="text-danger">*</span></label>
                    </div>
                </div>

                <!-- UF -->
                <div class="col-4 col-sm-2 col-md-2">
                    <div class="form-floating">
                        <input type="text" id="uf" name="uf" class="form-control text-uppercase font-monospace"
                            placeholder="UF" maxlength="2" value="<?= esc(old('uf')) ?>" required>
                        <label for="uf">UF <span class="text-danger">*</span></label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Forma de Pagamento -->
        <div class="checkout-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="checkout-step-badge bg-primary text-white">2</span>
                    <h2 class="fs-5 fw-bold text-dark mb-0">Forma de Pagamento</h2>
                </div>
                <small class="text-muted">Selecione uma opção</small>
            </div>

            <div class="row g-3 mb-4">
                <!-- Opção Pix -->
                <div class="col-sm-6">
                    <input type="radio" class="btn-check" name="forma_pagamento" id="pay_pix" value="pix"
                        <?= old('forma_pagamento', 'pix') === 'pix' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-light border text-dark w-100 p-3 text-start rounded-3 h-100 position-relative pay-method-label" for="pay_pix">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold d-flex align-items-center gap-2">
                                <i class="bi bi-qr-code text-success fs-4"></i> Pix
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size:0.75rem;">
                                Instantâneo
                            </span>
                        </div>
                        <small class="text-muted d-block mt-1">Aprovação imediata via QR Code</small>
                    </label>
                </div>

                <!-- Opção Cartão de Crédito -->
                <div class="col-sm-6">
                    <input type="radio" class="btn-check" name="forma_pagamento" id="pay_cartao" value="cartao_credito"
                        <?= old('forma_pagamento') === 'cartao_credito' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-light border text-dark w-100 p-3 text-start rounded-3 h-100 position-relative pay-method-label" for="pay_cartao">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold d-flex align-items-center gap-2">
                                <i class="bi bi-credit-card-2-front text-primary fs-4"></i> Cartão de Crédito
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size:0.75rem;">
                                Até 12x
                            </span>
                        </div>
                        <small class="text-muted d-block mt-1">Crédito à vista ou parcelado</small>
                    </label>
                </div>
            </div>

            <!-- Detalhes Pix Box -->
            <div id="box-pix-info" class="card bg-success-subtle border border-success-subtle rounded-3 p-3 mb-0">
                <div class="d-flex align-items-start gap-3">
                    <div class="p-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success-emphasis">Pagamento Rápido e Seguro via Pix</div>
                        <p class="text-muted small mb-0 mt-1">
                            Ao clicar em <strong>Confirmar Pedido</strong>, você verá o <strong>QR Code</strong> e a chave <strong>Copia e Cola</strong>. A confirmação é instantânea e seu pedido será aprovado em segundos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Campos de Cartão de Crédito Box -->
            <div id="box-cartao-campos" class="d-none card border bg-light rounded-3 p-3 mb-0">
                <?php if (ENVIRONMENT === 'development'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="badge bg-warning text-dark font-monospace" style="font-size:11px;">
                            <i class="bi bi-tools me-1"></i>Modo Dev
                        </span>
                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm py-1 px-2" id="btn-dev-fill-card" style="font-size:12px;">
                            <i class="bi bi-magic me-1"></i>Preencher Cartão de Teste
                        </button>
                    </div>
                <?php endif; ?>
                <div class="row g-3">
                    <!-- Número do Cartão -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted mb-1" for="cartao_numero">Número do Cartão</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" id="card-brand-icon">
                                <i class="bi bi-credit-card text-muted"></i>
                            </span>
                            <input type="text" id="cartao_numero" name="cartao_numero" class="form-control border-start-0 font-monospace"
                                placeholder="0000 0000 0000 0000" maxlength="19" value="<?= esc(old('cartao_numero')) ?>">
                        </div>
                    </div>

                    <!-- Nome Impresso -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted mb-1" for="cartao_nome">Nome no Cartão (como impresso)</label>
                        <input type="text" id="cartao_nome" name="cartao_nome" class="form-control text-uppercase"
                            placeholder="NOME COMPLETO" value="<?= esc(old('cartao_nome')) ?>">
                    </div>

                    <!-- Validade -->
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted mb-1" for="cartao_validade">Validade</label>
                        <input type="text" id="cartao_validade" name="cartao_validade" class="form-control font-monospace"
                            placeholder="MM/AA" maxlength="5" value="<?= esc(old('cartao_validade')) ?>">
                    </div>

                    <!-- CVV -->
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted mb-1" for="cartao_cvv">CVV</label>
                        <input type="text" id="cartao_cvv" name="cartao_cvv" class="form-control font-monospace"
                            placeholder="123" maxlength="4" value="<?= esc(old('cartao_cvv')) ?>">
                    </div>

                    <!-- Parcelas -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted mb-1" for="cartao_parcelas">Opções de Parcelamento</label>
                        <select id="cartao_parcelas" name="cartao_parcelas" class="form-select">
                            <?php for ($p = 1; $p <= 12; $p++): ?>
                                <?php $valorParc = $totalFinal / $p; ?>
                                <option value="<?= $p ?>" <?= (int)old('cartao_parcelas') === $p ? 'selected' : '' ?>>
                                    <?= $p ?>x de R$ <?= number_format($valorParc, 2, ',', '.') ?> <?= $p === 1 ? '(à vista)' : '(sem juros)' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ===== COLUNA DA DIREITA: RESUMO DO PEDIDO & AÇÕES ===== -->
    <div class="col-lg-5">
        <div class="checkout-summary-card p-4 checkout-sticky-summary">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h2 class="fs-5 fw-bold text-dark mb-0">Resumo do Pedido</h2>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill"><?= count($carrinho) ?> <?= count($carrinho) === 1 ? 'item' : 'itens' ?></span>
            </div>

            <!-- Lista de Produtos -->
            <div class="mb-3 overflow-auto" style="max-height: 220px;">
                <?php foreach ($carrinho as $item): ?>
                    <div class="d-flex align-items-center gap-3 py-2 border-bottom border-light-subtle">
                        <img src="<?= strpos($item['imagem'], 'http') === 0
                            ? esc($item['imagem'])
                            : base_url('uploads/produtos/' . esc($item['imagem'])) ?>"
                            alt="<?= esc($item['nome']) ?>"
                            class="checkout-item-thumb">
                        <div class="flex-grow-1 min-w-0">
                            <div class="small fw-semibold text-truncate text-dark"><?= esc($item['nome']) ?></div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                <?= $item['quantidade'] ?>x R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                <?php if (!empty($item['tamanho']) || !empty($item['cor'])): ?>
                                    <span class="badge bg-light text-dark border ms-1">
                                        <?= esc($item['tamanho'] ?? '') ?><?= !empty($item['tamanho']) && !empty($item['cor']) ? '/' : '' ?><?= esc($item['cor'] ?? '') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="small fw-bold text-dark text-nowrap">
                            R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Discriminação Financeira -->
            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>Subtotal</span>
                <span>R$ <?= esc(number_format($subtotal, 2, ',', '.')) ?></span>
            </div>

            <?php if ($desconto > 0): ?>
                <div class="d-flex justify-content-between small text-success mb-2">
                    <span><i class="bi bi-tag-fill me-1"></i>Desconto (<?= esc($cupomAtivo['codigo'] ?? '') ?>)</span>
                    <span>- R$ <?= esc(number_format($desconto, 2, ',', '.')) ?></span>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between small text-muted mb-3">
                <span>Frete <?= $freteAtivo ? '(' . esc($freteAtivo['modalidade']) . ')' : '' ?></span>
                <span class="text-success fw-semibold">
                    <?= $freteAtivo ? ((float)$freteAtivo['valor'] === 0.0 ? 'GRÁTIS' : 'R$ ' . number_format($freteAtivo['valor'], 2, ',', '.')) : 'A calcular' ?>
                </span>
            </div>

            <div class="border-top pt-3 d-flex justify-content-between align-items-center mb-4">
                <span class="fw-bold fs-5 text-dark">Total do Pedido</span>
                <span class="fw-bold fs-3 text-success" id="checkout-total-val">R$ <?= esc(number_format($totalFinal, 2, ',', '.')) ?></span>
            </div>

            <!-- Botão de Confirmação -->
            <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow d-flex align-items-center justify-content-center gap-2"
                style="border-radius:14px; font-size:1.1rem;" id="btn-confirmar-pedido">
                <i class="bi bi-bag-check-fill fs-5"></i>
                Confirmar Pedido
            </button>

            <!-- Links e Selos de Garantia -->
            <div class="text-center mt-3">
                <a href="<?= site_url('carrinho') ?>" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
                    <i class="bi bi-arrow-left"></i> Voltar e editar carrinho
                </a>
                <div class="pt-2 border-top">
                    <small class="text-muted d-flex align-items-center justify-content-center gap-1" style="font-size:0.75rem;">
                        <i class="bi bi-shield-fill-check text-success"></i> Garantia de Entrega & Pagamento Protegido
                    </small>
                </div>
            </div>

        </div>
    </div>

</div>

<?= form_close() ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------------------
    // Consulta de Endereço por CEP
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

            if (log && !log.value) log.value = data.logradouro || '';
            if (bai && !bai.value) bai.value = data.bairro     || '';
            if (cid && !cid.value) cid.value = data.localidade  || '';
            if (uf  && !uf.value)  uf.value  = data.uf          || '';
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
    // Alternância de Forma de Pagamento (Pix / Cartão de Crédito)
    // ----------------------------------------------------------------
    const radioPix    = document.getElementById('pay_pix');
    const radioCartao = document.getElementById('pay_cartao');
    const boxPix      = document.getElementById('box-pix-info');
    const boxCartao   = document.getElementById('box-cartao-campos');

    const inputNumCard  = document.getElementById('cartao_numero');
    const inputNomeCard = document.getElementById('cartao_nome');
    const inputValCard  = document.getElementById('cartao_validade');
    const inputCvvCard  = document.getElementById('cartao_cvv');
    const brandIcon     = document.getElementById('card-brand-icon');

    function alternarMetodoPagamento() {
        if (radioCartao && radioCartao.checked) {
            if (boxPix) boxPix.classList.add('d-none');
            if (boxCartao) boxCartao.classList.remove('d-none');

            if (inputNumCard) inputNumCard.setAttribute('required', 'required');
            if (inputNomeCard) inputNomeCard.setAttribute('required', 'required');
            if (inputValCard) inputValCard.setAttribute('required', 'required');
            if (inputCvvCard) inputCvvCard.setAttribute('required', 'required');
        } else {
            if (boxPix) boxPix.classList.remove('d-none');
            if (boxCartao) boxCartao.classList.add('d-none');

            if (inputNumCard) inputNumCard.removeAttribute('required');
            if (inputNomeCard) inputNomeCard.removeAttribute('required');
            if (inputValCard) inputValCard.removeAttribute('required');
            if (inputCvvCard) inputCvvCard.removeAttribute('required');
        }
    }

    if (radioPix && radioCartao) {
        radioPix.addEventListener('change', alternarMetodoPagamento);
        radioCartao.addEventListener('change', alternarMetodoPagamento);
        alternarMetodoPagamento(); // Inicializar estado
    }

    // Máscara e Detecção de Bandeira do Cartão
    if (inputNumCard) {
        inputNumCard.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 16);
            v = v.replace(/(\d{4})(?=\d)/g, '$1 ');
            this.value = v;

            const clean = v.replace(/\D/g, '');
            if (brandIcon) {
                if (/^4/.test(clean)) {
                    brandIcon.innerHTML = '<i class="bi bi-credit-card-2-front-fill text-primary" title="Visa"></i>';
                } else if (/^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[01]|2720)/.test(clean)) {
                    brandIcon.innerHTML = '<i class="bi bi-credit-card-2-front-fill text-warning" title="Mastercard"></i>';
                } else if (/^3[47]/.test(clean)) {
                    brandIcon.innerHTML = '<i class="bi bi-credit-card-2-front-fill text-info" title="American Express"></i>';
                } else if (/^((636368)|(438935)|(504175)|(451416)|(636297)|(5067)|(4576)|(4011))/.test(clean)) {
                    brandIcon.innerHTML = '<i class="bi bi-credit-card-2-front-fill text-danger" title="Elo"></i>';
                } else if (/^(606282|3841)/.test(clean)) {
                    brandIcon.innerHTML = '<i class="bi bi-credit-card-2-front-fill text-danger" title="Hipercard"></i>';
                } else {
                    brandIcon.innerHTML = '<i class="bi bi-credit-card text-muted"></i>';
                }
            }
        });
    }

    // Máscara de Validade do Cartão (MM/AA)
    if (inputValCard) {
        inputValCard.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 2) {
                v = v.substring(0, 2) + '/' + v.substring(2);
            }
            this.value = v;
        });
    }

    // Preenchimento Rápido em Ambiente de Desenvolvimento
    const btnDevFill = document.getElementById('btn-dev-fill-card');
    if (btnDevFill) {
        const testCards = [
            { num: '4532015012345678', nome: 'JOAO SILVA TESTE', val: '12/30', cvv: '123' },
            { num: '5522015012345678', nome: 'MARIA SANTOS TESTE', val: '08/29', cvv: '456' },
            { num: '4011785012345678', nome: 'PEDRO ALVES TESTE', val: '11/31', cvv: '789' }
        ];
        let testIdx = 0;

        btnDevFill.addEventListener('click', function () {
            const card = testCards[testIdx % testCards.length];
            testIdx++;

            if (inputNumCard) {
                inputNumCard.value = card.num;
                inputNumCard.dispatchEvent(new Event('input'));
            }
            if (inputNomeCard) {
                inputNomeCard.value = card.nome;
            }
            if (inputValCard) {
                inputValCard.value = card.val;
            }
            if (inputCvvCard) {
                inputCvvCard.value = card.cvv;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
