<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- ===== HERO BANNER ===== -->
    <section class="hero-banner mb-5">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>
            <div class="carousel-inner rounded-4 overflow-hidden shadow-sm">
                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&q=80&w=1600&h=500" class="d-block w-100 object-fit-cover" style="height: 500px;" alt="Nova Coleção">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="bg-dark bg-opacity-50 p-4 p-md-5 rounded-3 glassmorphism-overlay text-center">
                            <h2 class="display-4 fw-bold text-white mb-3">Nova Coleção Streetwear</h2>
                            <p class="lead text-light mb-4">Descubra as últimas tendências urbanas para o seu dia a dia.</p>
                            <a href="#lista-produtos" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold cta-button">Comprar Agora</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&q=80&w=1600&h=500" class="d-block w-100 object-fit-cover" style="height: 500px;" alt="Acessórios Exclusivos">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="bg-dark bg-opacity-50 p-4 p-md-5 rounded-3 glassmorphism-overlay text-center">
                            <h2 class="display-4 fw-bold text-white mb-3">Estilo que Marca</h2>
                            <p class="lead text-light mb-4">Acessórios exclusivos para complementar o seu visual.</p>
                            <a href="#lista-produtos" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold cta-button">Ver Coleção</a>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon rounded-circle bg-dark bg-opacity-50 p-3" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon rounded-circle bg-dark bg-opacity-50 p-3" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- ===== SWIMLANES: LANÇAMENTOS ===== -->
    <section class="swimlanes-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="fs-4 fw-bold mb-0">Lançamentos</h2>
            <a href="#lista-produtos" class="text-decoration-none text-primary fw-semibold">Ver tudo <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="swimlane-wrapper">
            <?php if (!empty($produtos) && is_array($produtos)): ?>
                <?php foreach (array_slice($produtos, 0, 6) as $produto): ?>
                    <?php $esgotado = (int)$produto['estoque'] === 0; ?>
                    <article class="product-card swimlane-card <?= $esgotado ? 'opacity-65' : '' ?>">
                        <div class="product-card-img-wrap">
                            <?php if (!empty($produto['imagem'])): ?>
                                <img src="<?= strpos($produto['imagem'] ?? '', 'http') === 0
                                    ? esc($produto['imagem'])
                                    : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                    alt="<?= esc($produto['nome']) ?>" loading="lazy">
                            <?php else: ?>
                                <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>"
                                    alt="Sem Imagem" loading="lazy">
                            <?php endif; ?>
                            <?php if ($esgotado): ?>
                                <span class="product-card-badge">Esgotado</span>
                            <?php else: ?>
                                <span class="product-card-badge bg-primary">Novo</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-body pb-0 position-relative z-1">
                            <h3 class="product-card-title text-truncate fs-6" title="<?= esc($produto['nome']) ?>"><?= esc($produto['nome']) ?></h3>
                            <p class="product-card-price mb-2 fs-5">R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></p>
                        </div>
                        <a href="<?= site_url('produto/' . $produto['id']) ?>" class="stretched-link z-2"></a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== PROMO BANNERS ===== -->
    <section class="promo-banners mb-5">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="promo-banner-card overflow-hidden rounded-4 position-relative shadow-sm">
                    <img src="https://images.unsplash.com/photo-1550639525-c97d455acf70?auto=format&fit=crop&q=80&w=800&h=400" class="w-100 h-100 object-fit-cover promo-img" alt="Promo Tênis">
                    <div class="promo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center p-4">
                        <span class="badge bg-danger mb-2 align-self-start fw-bold fs-6 shadow-sm">50% OFF</span>
                        <h3 class="text-white fw-bold mb-1 shadow-sm">Tênis Urbanos</h3>
                        <p class="text-light mb-3 fw-medium">Conforto e estilo para os seus pés.</p>
                        <a href="#lista-produtos" class="btn btn-light rounded-pill align-self-start fw-bold px-4 shadow-sm">Ver Ofertas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="promo-banner-card overflow-hidden rounded-4 position-relative shadow-sm">
                    <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&fit=crop&q=80&w=800&h=400" class="w-100 h-100 object-fit-cover promo-img" alt="Promo Camisetas">
                    <div class="promo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center p-4">
                        <span class="badge bg-warning text-dark mb-2 align-self-start fw-bold fs-6 shadow-sm">Compre 2 Leve 3</span>
                        <h3 class="text-white fw-bold mb-1 shadow-sm">Camisetas Premium</h3>
                        <p class="text-light mb-3 fw-medium">Combine e economize na nossa seleção.</p>
                        <a href="#lista-produtos" class="btn btn-light rounded-pill align-self-start fw-bold px-4 shadow-sm">Aproveitar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<div class="row g-4">

    <!-- ===== FILTER SIDEBAR (desktop) ===== -->
    <div class="col-lg-3 col-md-3 d-none d-md-block" id="filter-sidebar">
        <?php echo view('shop/_filter_panel') ?>
    </div>

    <!-- ===== OFFCANVAS FILTER (mobile) ===== -->
    <div class="offcanvas offcanvas-start filter-offcanvas" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="bi bi-funnel-fill text-primary me-2"></i>Filtros
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body p-0">
            <?php echo view('shop/_filter_panel') ?>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <button class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-dismiss="offcanvas" id="btn-apply-filters-mobile">
                <i class="bi bi-check-lg me-1"></i>Aplicar Filtros
            </button>
        </div>
    </div>

    <!-- ===== PRODUCT GRID ===== -->
    <div class="col-lg-9 col-md-9">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="fs-4 fw-bold mb-0" id="titulo-pagina"><?= esc($title) ?></h1>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4" id="lista-produtos">
            <?php if (!empty($produtos) && is_array($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                    <?php $esgotado = (int)$produto['estoque'] === 0; ?>
                    <div class="col">
                        <article class="product-card <?= $esgotado ? 'opacity-65' : '' ?>">
                            <div class="product-card-img-wrap">
                                <?php if (!empty($produto['imagem'])): ?>
                                    <img src="<?= strpos($produto['imagem'] ?? '', 'http') === 0
                                        ? esc($produto['imagem'])
                                        : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                        alt="<?= esc($produto['nome']) ?>" loading="lazy">
                                <?php else: ?>
                                    <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>"
                                        alt="Sem Imagem" loading="lazy">
                                <?php endif; ?>
                                <?php if ($esgotado): ?>
                                    <span class="product-card-badge">Esgotado</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-card-body">
                                <p class="product-card-category"><?= esc($produto['categoria_nome']) ?></p>
                                <h2 class="product-card-title"><?= esc($produto['nome']) ?></h2>
                                <p class="product-card-price">
                                    R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
                                </p>
                            </div>
                            <div class="product-card-footer">
                                <a href="<?= site_url('produto/' . $produto['id']) ?>"
                                    class="btn-details"
                                    id="ver-produto-<?= esc($produto['id']) ?>">
                                    <?php if ($esgotado): ?>
                                        <i class="bi bi-eye"></i> Ver Detalhes
                                    <?php else: ?>
                                        <i class="bi bi-bag-plus"></i> Ver Detalhes
                                    <?php endif; ?>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="bi bi-box-seam"></i></div>
                        <p class="fw-semibold mb-1">Nenhum produto encontrado</p>
                        <small>Tente outra categoria ou termo de busca.</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 d-flex justify-content-center pagination-container">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>

<!-- ===== FLOATING FILTER BUTTON (mobile only) ===== -->
<div class="filter-fab d-md-none" id="filter-fab">
    <button class="btn-filter-fab" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas" id="btn-fab-filtrar">
        <i class="bi bi-funnel-fill"></i>
        <span>Filtrar</span>
        <span class="filter-fab-badge d-none" id="filter-count-badge">0</span>
    </button>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formBusca        = document.getElementById('form-busca');
        const inputBusca       = document.getElementById('input-busca');
        const listaProdutos    = document.getElementById('lista-produtos');
        const pagerContainer   = document.querySelector('.pagination-container');
        const tituloPagina     = document.getElementById('titulo-pagina');
        const tituloOriginal   = tituloPagina?.textContent ?? '';

        if (!formBusca || !inputBusca || !listaProdutos) return;

        let debounceTimer;

        inputBusca.addEventListener('keyup', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => buscarProdutos(e.target.value.trim()), 300);
        });

        formBusca.addEventListener('submit', (e) => {
            e.preventDefault();
            buscarProdutos(inputBusca.value.trim());
        });

        async function buscarProdutos(termo) {
            if (termo.length === 0) {
                window.location.href = '<?= site_url('/') ?>';
                return;
            }
            try {
                const url      = `<?= site_url('api/produtos/busca') ?>?termo=${encodeURIComponent(termo)}`;
                const response = await fetch(url);
                const produtos = await response.json();
                renderizarProdutos(produtos, termo);
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
            }
        }

        function buildImageUrl(imagem) {
            if (!imagem) return '<?= base_url('uploads/produtos/sem_imagem.png') ?>';
            return imagem.startsWith('http')
                ? imagem
                : `<?= base_url('uploads/produtos/') ?>${imagem}`;
        }

        function renderizarProdutos(produtos, termo) {
            listaProdutos.innerHTML = '';
            if (tituloPagina) tituloPagina.textContent = `Resultados para: "${termo}"`;
            if (pagerContainer) pagerContainer.style.display = 'none';

            if (produtos.length === 0) {
                listaProdutos.innerHTML = `
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-search"></i></div>
                            <p class="fw-semibold mb-1">Nenhum produto encontrado</p>
                            <small>Tente outro termo de busca.</small>
                        </div>
                    </div>`;
                return;
            }

            produtos.forEach(produto => {
                const preco    = parseFloat(produto.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const imgUrl   = buildImageUrl(produto.imagem);
                const url      = `<?= site_url('produto/') ?>${produto.id}`;
                const esgotado = parseInt(produto.estoque) === 0;
                const badge    = esgotado ? `<span class="product-card-badge">Esgotado</span>` : '';
                const opacity  = esgotado ? 'opacity-65' : '';
                const icon     = esgotado ? 'bi-eye' : 'bi-bag-plus';

                listaProdutos.innerHTML += `
                    <div class="col">
                        <article class="product-card ${opacity}">
                            <div class="product-card-img-wrap">
                                <img src="${imgUrl}" alt="${produto.nome}" loading="lazy">
                                ${badge}
                            </div>
                            <div class="product-card-body">
                                <p class="product-card-category">${produto.categoria_nome ?? ''}</p>
                                <h2 class="product-card-title">${produto.nome}</h2>
                                <p class="product-card-price">${preco}</p>
                            </div>
                            <div class="product-card-footer">
                                <a href="${url}" class="btn-details">
                                    <i class="bi ${icon}"></i> Ver Detalhes
                                </a>
                            </div>
                        </article>
                    </div>`;
            });
        }
    });
</script>

<script>
    /* ============================================================
       Filter Panel — Price Range Slider + Active Filter Counter
       ============================================================ */
    (function () {
        const rangeMin    = document.getElementById('price-range-min');
        const rangeMax    = document.getElementById('price-range-max');
        const inputMin    = document.getElementById('price-input-min');
        const inputMax    = document.getElementById('price-input-max');
        const rangeFill   = document.getElementById('range-fill');
        const fabBadge    = document.getElementById('filter-count-badge');
        const filterFab   = document.getElementById('filter-fab');
        const productGrid = document.getElementById('lista-produtos');

        function updateRangeTrack() {
            if (!rangeMin || !rangeMax || !rangeFill) return;
            const min  = parseInt(rangeMin.min);
            const max  = parseInt(rangeMax.max);
            const valMin = parseInt(rangeMin.value);
            const valMax = parseInt(rangeMax.value);
            const pctMin = ((valMin - min) / (max - min)) * 100;
            const pctMax = ((valMax - min) / (max - min)) * 100;
            rangeFill.style.left  = pctMin + '%';
            rangeFill.style.width = (pctMax - pctMin) + '%';
        }

        function syncPriceInputs() {
            if (!rangeMin || !inputMin) return;
            inputMin.value = rangeMin.value;
            inputMax.value = rangeMax.value;
            updateRangeTrack();
            countActiveFilters();
        }

        function syncRangeFromInputs() {
            if (!inputMin || !rangeMin) return;
            const vMin = Math.min(parseInt(inputMin.value) || 0, parseInt(inputMax.value) - 1);
            const vMax = Math.max(parseInt(inputMax.value) || 5000, parseInt(inputMin.value) + 1);
            rangeMin.value = vMin;
            rangeMax.value = vMax;
            inputMin.value = vMin;
            inputMax.value = vMax;
            updateRangeTrack();
            countActiveFilters();
        }

        function countActiveFilters() {
            if (!fabBadge) return;
            const checkboxes = document.querySelectorAll('.filter-check:checked');
            const priceChanged = rangeMin && (parseInt(rangeMin.value) > parseInt(rangeMin.min)
                || parseInt(rangeMax.value) < parseInt(rangeMax.max));
            const total = checkboxes.length + (priceChanged ? 1 : 0);
            fabBadge.textContent = total;
            fabBadge.classList.toggle('d-none', total === 0);
        }

        /* Show/hide FAB on scroll */
        function handleFabVisibility() {
            if (!filterFab || !productGrid) return;
            const rect = productGrid.getBoundingClientRect();
            filterFab.classList.toggle('filter-fab--visible', rect.top < window.innerHeight && rect.bottom > 0);
        }

        if (rangeMin) rangeMin.addEventListener('input', syncPriceInputs);
        if (rangeMax) rangeMax.addEventListener('input', syncPriceInputs);
        if (inputMin) inputMin.addEventListener('change', syncRangeFromInputs);
        if (inputMax) inputMax.addEventListener('change', syncRangeFromInputs);

        document.querySelectorAll('.filter-check').forEach(cb => {
            cb.addEventListener('change', countActiveFilters);
        });

        window.addEventListener('scroll', handleFabVisibility, { passive: true });

        /* Init */
        updateRangeTrack();
        countActiveFilters();
        handleFabVisibility();
    }());
</script>
<?= $this->endSection() ?>