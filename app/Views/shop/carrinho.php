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

    <div class="row g-4">

        <!-- ===== CART ITEMS ===== -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table cart-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:80px;">Produto</th>
                                    <th></th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                foreach ($carrinho as $id => $item):
                                    $subtotal = $item['preco'] * $item['quantidade'];
                                    $total   += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?= strpos($item['imagem'], 'http') === 0
                                            ? esc($item['imagem'])
                                            : base_url('uploads/produtos/' . esc($item['imagem'])) ?>"
                                            alt="<?= esc($item['nome']) ?>"
                                            class="img-thumb-table" style="width:52px;height:52px;">
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
                                                style="border-radius:8px; white-space:nowrap;"
                                                id="btn-atualizar-<?= $id ?>">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        <?= form_close() ?>
                                    </td>
                                    <td class="fw-semibold">
                                        R$ <?= esc(number_format($subtotal, 2, ',', '.')) ?>
                                    </td>
                                    <td>
                                        <?= form_open('carrinho/remover/' . $id, ['class' => 'd-inline']) ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;"
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
        </div>

        <!-- ===== ORDER SUMMARY ===== -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px;">
                <div class="card-body">
                    <h2 class="fs-6 fw-bold mb-3 text-muted text-uppercase"
                        style="letter-spacing:.07em; font-size:.75rem !important;">
                        Resumo do Pedido
                    </h2>
                    <div class="d-flex justify-content-between mb-2 fs-6">
                        <span class="text-muted">Subtotal</span>
                        <span>R$ <?= esc(number_format($total, 2, ',', '.')) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 fs-6">
                        <span class="text-muted">Frete</span>
                        <span class="text-success fw-semibold">A calcular</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-5">Total</strong>
                        <strong class="fs-5 text-success">
                            R$ <?= esc(number_format($total, 2, ',', '.')) ?>
                        </strong>
                    </div>

                    <!-- Trigger checkout offcanvas -->
                    <button class="btn btn-primary w-100 py-3 fw-bold"
                        style="border-radius:12px; font-size:1rem;"
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
                            placeholder="00000-000" maxlength="9" required>
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

            <div class="border-top mt-4 pt-4">
                <div class="d-flex justify-content-between mb-3 fw-semibold fs-5">
                    <span>Total do pedido</span>
                    <span class="text-success">R$ <?= esc(number_format($total, 2, ',', '.')) ?></span>
                </div>
                <button type="submit" class="btn btn-success w-100 py-3 fw-bold"
                    style="border-radius:12px; font-size:1rem;" id="btn-confirmar-pedido">
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
    const cepInput = document.getElementById('cep');
    const feedback = document.getElementById('cep-feedback');

    if (!cepInput) return;

    cepInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
    });

    cepInput.addEventListener('blur', async function () {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        feedback.classList.add('d-none');

        try {
            const res  = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await res.json();

            if (data.erro) { feedback.classList.remove('d-none'); return; }

            document.getElementById('logradouro').value = data.logradouro || '';
            document.getElementById('bairro').value     = data.bairro     || '';
            document.getElementById('cidade').value     = data.localidade  || '';
            document.getElementById('uf').value         = data.uf          || '';
            document.getElementById('numero').focus();
        } catch (e) {
            feedback.classList.remove('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>
