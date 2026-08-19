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
            $p = (!empty($var['preco']) && (float)$var['preco'] > 0) ? (float)$var['preco'] : (float)$produto['preco'];
            
            $variacoesData[] = [
                'id'      => (int)$var['id'],
                'tamanho' => $t,
                'cor'     => $c,
                'preco'   => $p,
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

    // Determinar Rótulo Dinâmico para a Variação / Atributo
    $rotuloVariacao = 'Variação / Opção';
    if (!empty($tamanhosDisponiveis)) {
        $isVoltagem = true;
        $isCapacidade = true;
        $isTamanho = true;
        foreach ($tamanhosDisponiveis as $itemVal) {
            $valUpper = strtoupper(trim($itemVal));
            if (!preg_match('/^(110V|220V|127V|BIVOLT|\d+V)$/i', $valUpper)) {
                $isVoltagem = false;
            }
            if (!preg_match('/^\d+\s*(GB|TB|MB|G|T)$/i', $valUpper)) {
                $isCapacidade = false;
            }
            if (!preg_match('/^(PP|P|M|G|GG|XG|XGG|XXG|ÚNICO|UNICO|\d{2})$/i', $valUpper)) {
                $isTamanho = false;
            }
        }
        if ($isCapacidade) {
            $rotuloVariacao = 'Capacidade / Modelo';
        } elseif ($isVoltagem) {
            $rotuloVariacao = 'Voltagem';
        } elseif ($isTamanho) {
            $rotuloVariacao = 'Tamanho';
        } else {
            $rotuloVariacao = 'Variação / Opção';
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

            <div class="price-tag mb-3" id="pdp-price">
                R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
            </div>

            <!-- Stock Indicator -->
            <?php if (!$esgotado): ?>
                <div class="pdp-stock-badge pdp-stock-available" id="pdp-stock-badge">
                    <i class="bi bi-check-circle-fill"></i>
                    <span id="pdp-stock-text"><?= esc($produto['estoque']) ?> unidades em estoque</span>
                </div>
            <?php else: ?>
                <div class="pdp-stock-badge pdp-stock-unavailable" id="pdp-stock-badge">
                    <i class="bi bi-x-circle-fill"></i>
                    <span id="pdp-stock-text">Esgotado</span>
                </div>
            <?php endif; ?>

            <!-- Variation / Attribute Selector -->
            <?php if (!empty($tamanhosDisponiveis)): ?>
            <div class="pdp-variant-section">
                <p class="pdp-variant-label" id="pdp-variant-type-label"><?= esc($rotuloVariacao) ?></p>
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

            <!-- Color Selector (Optional) -->
            <?php if (!empty($coresDisponiveis)): ?>
            <div class="pdp-variant-section">
                <p class="pdp-variant-label">Cor</p>
                <div class="pdp-variant-options d-flex flex-wrap gap-2" id="color-options">
                    <?php foreach ($coresDisponiveis as $index => $cor): ?>
                        <?php $isHex = preg_match('/^#([a-f0-9]{3}){1,2}\b/i', $cor); ?>
                        <label class="pdp-color-chip variant-color-label" data-color="<?= esc($cor) ?>" title="<?= esc($cor) ?>">
                            <input type="radio" name="cor" value="<?= esc($cor) ?>" class="variant-color-selector">
                            <?php if ($isHex): ?>
                                <span class="pdp-color-swatch border border-secondary-subtle shadow-sm" style="background-color: <?= esc($cor) ?>;">
                                    <i class="bi bi-check2"></i>
                                </span>
                            <?php else: ?>
                                <span class="pdp-variant-chip variant-size-label d-inline-block px-3 py-1 border rounded-pill small fw-semibold"><?= esc($cor) ?></span>
                            <?php endif; ?>
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

            <!-- ===== SIMULADOR DE FRETE ===== -->
            <div class="pdp-frete-card card border-0 shadow-sm mt-4 p-3 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-6">
                        <i class="bi bi-truck text-primary me-2"></i>Calcular Frete e Prazo
                    </span>
                    <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank" class="small text-muted text-decoration-none">
                        Não sei meu CEP
                    </a>
                </div>
                <div class="input-group mb-2">
                    <input type="text" id="cep-calculo" class="form-control rounded-start-pill font-monospace"
                           placeholder="00000-000" maxlength="9" aria-label="CEP para entrega">
                    <button class="btn btn-primary rounded-end-pill px-4 fw-semibold" type="button" id="btn-calcular-frete">
                        Calcular
                    </button>
                </div>
                <div id="resultado-frete-pdp" class="mt-2"></div>
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
    const basePreco = <?= (float) $produto['preco'] ?>;
    const baseEstoque = <?= (int) $produto['estoque'] ?>;
    const btnAddCart = document.getElementById('btn-add-cart');
    const inputVariacaoId = document.getElementById('variacao_id');
    const priceDisplay = document.getElementById('pdp-price');
    const stockText = document.getElementById('pdp-stock-text');
    
    const formAddCart = document.getElementById('form-add-cart');
    if (formAddCart) {
        formAddCart.addEventListener('submit', function(e) {
            if (variacoes.length > 0 && !inputVariacaoId.value) {
                e.preventDefault();
                alert('Por favor, selecione uma opção disponível antes de adicionar ao carrinho.');
            }
        });
    }
    const sizeRadios = document.querySelectorAll('.variant-size-selector');
    const colorRadios = document.querySelectorAll('.variant-color-selector');
    const hasSizes = sizeRadios.length > 0;
    const hasColors = colorRadios.length > 0;

    function formatMoney(valor) {
        return 'R$ ' + Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

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
                    if(input && input.checked) { input.checked = false; selectedSize = null; }
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
                    if(input && input.checked) { input.checked = false; selectedColor = null; }
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
            
            // Atualizar preço dinâmico na tela
            if (priceDisplay) {
                priceDisplay.innerText = formatMoney(matchingVar.preco);
            }
            if (stockText) {
                stockText.innerText = matchingVar.estoque + ' unidades em estoque';
            }
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
            if (priceDisplay) {
                priceDisplay.innerText = formatMoney(basePreco);
            }
            if (stockText) {
                stockText.innerText = baseEstoque + ' unidades em estoque';
            }
        }
    }

    sizeRadios.forEach(radio => radio.addEventListener('change', checkVariations));
    colorRadios.forEach(radio => radio.addEventListener('change', checkVariations));

    if (variacoes.length > 0 && btnAddCart) {
        btnAddCart.disabled = true;
        btnAddCart.classList.add('pdp-add-to-cart--disabled');
        checkVariations();
    }

    // --- Simulador de Frete PDP ---
    const cepInput = document.getElementById('cep-calculo');
    const btnCalcularFrete = document.getElementById('btn-calcular-frete');
    const resultadoFrete = document.getElementById('resultado-frete-pdp');

    if (cepInput && btnCalcularFrete && resultadoFrete) {
        cepInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
        });

        cepInput.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                btnCalcularFrete.click();
            }
        });

        btnCalcularFrete.addEventListener('click', async function () {
            const cep = cepInput.value.replace(/\D/g, '');
            if (cep.length !== 8) {
                resultadoFrete.innerHTML = `
                    <div class="alert alert-warning py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>Digite um CEP válido com 8 dígitos.
                    </div>`;
                return;
            }

            resultadoFrete.innerHTML = `
                <div class="text-center py-3 text-muted small">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Calculando opções de frete...
                </div>`;

            try {
                const formData = new FormData();
                formData.append('cep', cep);
                formData.append('produto_id', '<?= esc($produto['id']) ?>');
                formData.append('quantidade', qtyInput ? qtyInput.value : 1);

                const response = await fetch('<?= site_url('api/frete/calcular') ?>', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (!data.ok) {
                    resultadoFrete.innerHTML = `
                        <div class="alert alert-danger py-2 px-3 small mb-0 rounded-3">
                            <i class="bi bi-exclamation-circle me-1"></i>${data.erro || 'Não foi possível calcular o frete.'}
                        </div>`;
                    return;
                }

                let html = `<div class="list-group list-group-flush rounded-3 border">`;
                data.opcoes.forEach(op => {
                    const precoFmt = op.valor === 0
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-6">GRÁTIS</span>`
                        : `<strong class="text-success fs-6">R$ ${op.valor.toFixed(2).replace('.', ',')}</strong>`;

                    html += `
                        <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 ${op.destaque ? 'bg-success-subtle bg-opacity-25' : ''}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi ${op.icone} text-primary fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">${op.nome}</div>
                                    <small class="text-muted" style="font-size:0.75rem;">Chega em ${op.prazo}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                ${precoFmt}
                            </div>
                        </div>`;
                });
                html += `</div>`;
                resultadoFrete.innerHTML = html;
            } catch (err) {
                console.error(err);
                resultadoFrete.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-exclamation-circle me-1"></i>Erro ao calcular frete. Tente novamente.
                    </div>`;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>