<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $imagemPrincipal = !empty($produto['imagem'])
        ? (strpos($produto['imagem'], 'http') === 0
            ? esc($produto['imagem'])
            : base_url('uploads/produtos/' . esc($produto['imagem'])))
        : base_url('uploads/produtos/sem_imagem.png');

    // Galeria Real + Imagem Principal
    $galeria = [$imagemPrincipal];
    if (!empty($imagens) && is_array($imagens)) {
        foreach ($imagens as $img) {
            $isUrl = (strpos($img['caminho_imagem'], 'http://') === 0 || strpos($img['caminho_imagem'], 'https://') === 0);
            $galeria[] = $isUrl ? esc($img['caminho_imagem']) : base_url('uploads/produtos/' . esc($img['caminho_imagem']));
        }
    }

    $esgotado = (int)$produto['estoque'] === 0;

    // Processar Variações reais do Banco
    $tamanhosDisponiveis = [];
    $coresDisponiveis = [];
    $variacoesData = [];

    if (!empty($variacoes) && is_array($variacoes)) {
        foreach ($variacoes as $var) {
            $t = trim($var['tamanho'] ?? '');
            $c = trim($var['cor'] ?? '');
            
            $variacoesData[] = [
                'id' => $var['id'],
                'tamanho' => $t,
                'cor' => $c,
                'estoque' => (int)$var['estoque']
            ];
            
            if ($t !== '' && !in_array($t, $tamanhosDisponiveis)) {
                $tamanhosDisponiveis[] = $t;
            }
            if ($c !== '' && !in_array($c, $coresDisponiveis)) {
                $coresDisponiveis[] = $c;
            }
        }
    }
?>

<!-- ===== BREADCRUMB ===== -->
<nav aria-label="breadcrumb" class="mb-4" id="breadcrumb-nav">
    <ol class="breadcrumb breadcrumb-custom">
        <li class="breadcrumb-item">
            <a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= site_url('categoria/' . esc($produto['categoria_id'])) ?>"><?= esc($produto['categoria_nome']) ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($produto['nome']) ?></li>
    </ol>
</nav>

<div class="row g-4 g-lg-5 align-items-start">

    <!-- ===== IMAGE GALLERY ===== -->
    <div class="col-lg-6">
        <div class="pdp-gallery">
            <!-- Main Image -->
            <div class="pdp-gallery-main mb-3">
                <img src="<?= $galeria[0] ?>"
                     alt="<?= esc($produto['nome']) ?>"
                     class="pdp-main-img"
                     id="pdp-main-img">
            </div>
            <!-- Thumbnails -->
            <?php if (count($galeria) > 1): ?>
            <div class="pdp-thumbnails" id="pdp-thumbnails">
                <?php foreach ($galeria as $index => $thumb): ?>
                    <button type="button"
                            class="pdp-thumb-btn <?= $index === 0 ? 'active' : '' ?>"
                            data-img="<?= $thumb ?>"
                            aria-label="Miniatura <?= $index + 1 ?>">
                        <img src="<?= $thumb ?>" alt="Miniatura <?= $index + 1 ?>" loading="lazy">
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== PRODUCT INFO ===== -->
    <div class="col-lg-6">
        <div class="pdp-info">

            <!-- Category tag -->
            <a href="<?= site_url('categoria/' . esc($produto['categoria_id'])) ?>" class="pdp-category-tag" id="pdp-category-link">
                <i class="bi bi-tag-fill me-1"></i><?= esc($produto['categoria_nome']) ?>
            </a>

            <h1 class="pdp-title" id="pdp-title"><?= esc($produto['nome']) ?></h1>

            <div class="price-tag mb-3">
                R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
            </div>

            <!-- Stock Indicator -->
            <?php if (!$esgotado): ?>
                <div class="pdp-stock-badge pdp-stock-available">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= esc($produto['estoque']) ?> unidades em estoque
                </div>
            <?php else: ?>
                <div class="pdp-stock-badge pdp-stock-unavailable">
                    <i class="bi bi-x-circle-fill"></i> Esgotado
                </div>
            <?php endif; ?>

            <!-- Size Selector -->
            <?php if (!empty($tamanhosDisponiveis)): ?>
            <div class="pdp-variant-section">
                <p class="pdp-variant-label">Tamanho</p>
                <div class="pdp-variant-options" id="size-options">
                    <?php foreach ($tamanhosDisponiveis as $index => $tamanho): ?>
                        <label class="pdp-variant-chip variant-size-label" data-size="<?= esc($tamanho) ?>">
                            <input type="radio" name="tamanho" value="<?= esc($tamanho) ?>" class="variant-size-selector">
                            <span><?= esc($tamanho) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Color Selector -->
            <?php if (!empty($coresDisponiveis)): ?>
            <div class="pdp-variant-section">
                <p class="pdp-variant-label">Cor</p>
                <div class="pdp-variant-options" id="color-options">
                    <?php foreach ($coresDisponiveis as $index => $cor): ?>
                        <label class="pdp-color-chip variant-color-label" data-color="<?= esc($cor) ?>" title="<?= esc($cor) ?>">
                            <input type="radio" name="cor" value="<?= esc($cor) ?>" class="variant-color-selector">
                            <span class="pdp-color-swatch border border-secondary-subtle shadow-sm" style="background-color: <?= esc($cor) ?>;">
                                <i class="bi bi-check2"></i>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Description -->
            <div class="pdp-description">
                <h2 class="pdp-variant-label">Descrição</h2>
                <p><?= esc($produto['descricao']) ?></p>
            </div>

            <!-- Add to Cart -->
            <div class="pdp-cart-actions">
                <?php if (!$esgotado): ?>
                    <?= form_open('carrinho/adicionar', ['id' => 'form-add-cart']) ?>
                        <input type="hidden" name="produto_id" value="<?= esc($produto['id']) ?>">
                        <input type="hidden" name="variacao_id" id="variacao_id" value="">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="pdp-qty-wrap">
                                <button type="button" class="pdp-qty-btn" id="qty-minus" aria-label="Diminuir">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <input type="number" name="quantidade" id="quantidade"
                                       class="pdp-qty-input"
                                       value="1" min="1"
                                       max="<?= esc($produto['estoque']) ?>">
                                <button type="button" class="pdp-qty-btn" id="qty-plus" aria-label="Aumentar">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>

                            <button type="submit" class="pdp-add-to-cart flex-grow-1" id="btn-add-cart">
                                <i class="bi bi-bag-plus-fill me-2"></i>
                                Adicionar ao Carrinho
                            </button>
                        </div>
                    <?= form_close() ?>
                <?php else: ?>
                    <button class="pdp-add-to-cart pdp-add-to-cart--disabled w-100" disabled>
                        <i class="bi bi-bag-x me-2"></i>Produto Esgotado
                    </button>
                <?php endif; ?>
            </div>

            <!-- Trust Badges -->
            <div class="pdp-trust-badges">
                <?php if ($produto['frete_gratis']): ?>
                <div class="pdp-trust-item">
                    <i class="bi bi-truck"></i>
                    <span>Frete Grátis</span>
                </div>
                <?php endif; ?>
                <div class="pdp-trust-item">
                    <i class="bi bi-arrow-return-left"></i>
                    <span>Devolução Grátis</span>
                </div>
                <div class="pdp-trust-item">
                    <i class="bi bi-shield-check"></i>
                    <span>Compra Segura</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== RELATED PRODUCTS ===== -->
<?php if (!empty($relacionados)): ?>
<section class="pdp-related-section mt-5 pt-4 border-top" id="produtos-relacionados">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fs-4 fw-bold mb-0">Você também pode gostar</h2>
        <a href="<?= site_url('categoria/' . esc($produto['categoria_id'])) ?>"
           class="text-decoration-none text-primary fw-semibold">
            Ver mais <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="row row-cols-2 row-cols-md-4 g-4">
        <?php foreach ($relacionados as $rel): ?>
            <?php $relEsgotado = (int)$rel['estoque'] === 0; ?>
            <div class="col">
                <article class="product-card <?= $relEsgotado ? 'opacity-65' : '' ?>">
                    <div class="product-card-img-wrap">
                        <?php if (!empty($rel['imagem'])): ?>
                            <img src="<?= strpos($rel['imagem'], 'http') === 0
                                ? esc($rel['imagem'])
                                : base_url('uploads/produtos/' . esc($rel['imagem'])) ?>"
                                alt="<?= esc($rel['nome']) ?>" loading="lazy">
                        <?php else: ?>
                            <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>"
                                alt="Sem Imagem" loading="lazy">
                        <?php endif; ?>
                        <?php if ($relEsgotado): ?>
                            <span class="product-card-badge">Esgotado</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-card-body">
                        <p class="product-card-category"><?= esc($rel['categoria_nome']) ?></p>
                        <h3 class="product-card-title"><?= esc($rel['nome']) ?></h3>
                        <p class="product-card-price">
                            R$ <?= esc(number_format($rel['preco'], 2, ',', '.')) ?>
                        </p>
                    </div>
                    <div class="product-card-footer">
                        <a href="<?= site_url('produto/' . $rel['id']) ?>"
                           class="btn-details"
                           id="ver-relacionado-<?= esc($rel['id']) ?>">
                            <i class="bi bi-bag-plus"></i> Ver Detalhes
                        </a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImg    = document.getElementById('pdp-main-img');
    const thumbsWrap = document.getElementById('pdp-thumbnails');

    if (thumbsWrap && mainImg) {
        thumbsWrap.addEventListener('click', function (e) {
            const btn = e.target.closest('.pdp-thumb-btn');
            if (!btn) return;

            thumbsWrap.querySelectorAll('.pdp-thumb-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = btn.dataset.img;
                mainImg.style.opacity = '1';
            }, 180);
        });
    }

    const qtyInput = document.getElementById('quantidade');
    const btnMinus = document.getElementById('qty-minus');
    const btnPlus  = document.getElementById('qty-plus');

    if (qtyInput && btnMinus && btnPlus) {
        btnMinus.addEventListener('click', () => {
            const current = parseInt(qtyInput.value) || 1;
            if (current > 1) qtyInput.value = current - 1;
        });

        btnPlus.addEventListener('click', () => {
            const current = parseInt(qtyInput.value) || 1;
            const max     = parseInt(qtyInput.max) || 999;
            if (current < max) qtyInput.value = current + 1;
        });
    }

    // --- Lógica de Variações ---
    const variacoes = <?= json_encode($variacoesData ?? []) ?>;
    const btnAddCart = document.getElementById('btn-add-cart');
    const inputVariacaoId = document.getElementById('variacao_id');
    
    const formAddCart = document.getElementById('form-add-cart');
    if (formAddCart) {
        formAddCart.addEventListener('submit', function(e) {
            if (variacoes.length > 0 && !inputVariacaoId.value) {
                e.preventDefault();
                alert('Por favor, selecione um tamanho e/ou cor disponíveis antes de adicionar ao carrinho.');
            }
        });
    }
    const sizeRadios = document.querySelectorAll('.variant-size-selector');
    const colorRadios = document.querySelectorAll('.variant-color-selector');
    const hasSizes = sizeRadios.length > 0;
    const hasColors = colorRadios.length > 0;

    function checkVariations() {
        if (!hasSizes && !hasColors) return;

        let selectedSize = null;
        let selectedColor = null;

        if (hasSizes) {
            const checkedSize = document.querySelector('.variant-size-selector:checked');
            if (checkedSize) selectedSize = checkedSize.value;
        }

        if (hasColors) {
            const checkedColor = document.querySelector('.variant-color-selector:checked');
            if (checkedColor) selectedColor = checkedColor.value;
        }

        if (hasSizes) {
            document.querySelectorAll('.variant-size-label').forEach(label => {
                const sizeVal = label.dataset.size;
                const available = variacoes.some(v => v.tamanho === sizeVal && (!hasColors || !selectedColor || v.cor === selectedColor) && v.estoque > 0);
                if (!available) {
                    label.style.opacity = '0.4';
                    label.style.textDecoration = 'line-through';
                    const input = label.querySelector('input');
                    if(input.checked) { input.checked = false; selectedSize = null; }
                } else {
                    label.style.opacity = '1';
                    label.style.textDecoration = 'none';
                }
            });
        }

        if (hasColors) {
            document.querySelectorAll('.variant-color-label').forEach(label => {
                const colorVal = label.dataset.color;
                const available = variacoes.some(v => v.cor === colorVal && (!hasSizes || !selectedSize || v.tamanho === selectedSize) && v.estoque > 0);
                if (!available) {
                    label.style.opacity = '0.3';
                    const input = label.querySelector('input');
                    if(input.checked) { input.checked = false; selectedColor = null; }
                } else {
                    label.style.opacity = '1';
                }
            });
        }

        let matchingVar = null;
        if ((!hasSizes || selectedSize) && (!hasColors || selectedColor)) {
            matchingVar = variacoes.find(v => 
                (!hasSizes || v.tamanho === selectedSize) && 
                (!hasColors || v.cor === selectedColor)
            );
        }

        if (matchingVar && matchingVar.estoque > 0) {
            inputVariacaoId.value = matchingVar.id;
            btnAddCart.disabled = false;
            btnAddCart.classList.remove('pdp-add-to-cart--disabled');
            if(qtyInput) {
                qtyInput.max = matchingVar.estoque;
                if(parseInt(qtyInput.value) > matchingVar.estoque) {
                    qtyInput.value = matchingVar.estoque;
                }
            }
        } else {
            inputVariacaoId.value = '';
            btnAddCart.disabled = true;
            btnAddCart.classList.add('pdp-add-to-cart--disabled');
        }
    }

    sizeRadios.forEach(radio => radio.addEventListener('change', checkVariations));
    colorRadios.forEach(radio => radio.addEventListener('change', checkVariations));

    if (variacoes.length > 0 && btnAddCart) {
        btnAddCart.disabled = true;
        btnAddCart.classList.add('pdp-add-to-cart--disabled');
        checkVariations();
    }
});
</script>
<?= $this->endSection() ?>