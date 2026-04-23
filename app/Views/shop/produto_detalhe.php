<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row align-items-start g-5 mt-1">

    <!-- ===== PRODUCT IMAGE ===== -->
    <div class="col-md-5">
        <?php if (!empty($produto['imagem'])): ?>
            <img src="<?= strpos($produto['imagem'], 'http') === 0
                ? esc($produto['imagem'])
                : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                class="product-detail-img"
                alt="<?= esc($produto['nome']) ?>">
        <?php else: ?>
            <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>"
                class="product-detail-img" alt="Sem Imagem">
        <?php endif; ?>
    </div>

    <!-- ===== PRODUCT INFO ===== -->
    <div class="col-md-7">

        <!-- Breadcrumb category -->
        <p class="text-primary fw-semibold" style="font-size:.8125rem; text-transform:uppercase; letter-spacing:.06em;">
            <i class="bi bi-tag me-1"></i><?= esc($produto['categoria_nome']) ?>
        </p>

        <h1 class="fw-bold mb-3" style="font-size:1.75rem; letter-spacing:-.025em;">
            <?= esc($produto['nome']) ?>
        </h1>

        <div class="price-tag mb-3">
            R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
        </div>

        <!-- Stock -->
        <?php if ((int)$produto['estoque'] > 0): ?>
            <div class="d-inline-flex align-items-center gap-2 mb-4 px-3 py-2 rounded-pill"
                style="background:#ecfdf5; color:#065f46; font-size:.875rem; font-weight:600;">
                <i class="bi bi-check-circle-fill"></i>
                <?= esc($produto['estoque']) ?> unidades em estoque
            </div>
        <?php else: ?>
            <span class="badge rounded-pill px-3 py-2 mb-4 d-inline-flex align-items-center gap-1"
                style="background:#fef2f2; color:#991b1b; font-size:.875rem;">
                <i class="bi bi-x-circle-fill"></i> Esgotado
            </span>
        <?php endif; ?>

        <!-- Description -->
        <div class="mb-4">
            <h2 class="fs-6 fw-bold mb-2 text-muted text-uppercase" style="letter-spacing:.06em; font-size:.75rem !important;">
                Descrição
            </h2>
            <p class="text-secondary lh-lg"><?= esc($produto['descricao']) ?></p>
        </div>

        <!-- Add to cart -->
        <div class="border-top pt-4">
            <?php if ((int)$produto['estoque'] > 0): ?>
                <?= form_open('carrinho/adicionar') ?>
                    <input type="hidden" name="produto_id" value="<?= esc($produto['id']) ?>">

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="form-floating" style="max-width:120px;">
                            <input type="number" name="quantidade" id="quantidade"
                                class="form-control"
                                value="1" min="1"
                                max="<?= esc($produto['estoque']) ?>">
                            <label for="quantidade">Qtd.</label>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-3 fw-bold flex-grow-1"
                            style="border-radius:12px; font-size:1rem;" id="btn-add-cart">
                            <i class="bi bi-bag-plus-fill me-2"></i>
                            Adicionar ao Carrinho
                        </button>
                    </div>
                <?= form_close() ?>
            <?php else: ?>
                <button class="btn btn-secondary w-100 py-3 fw-bold" style="border-radius:12px; font-size:1rem;" disabled>
                    <i class="bi bi-bag-x me-2"></i>Produto Esgotado
                </button>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>