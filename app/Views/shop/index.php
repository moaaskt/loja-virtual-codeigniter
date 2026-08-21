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
                                <button type="button" class="btn-favorite-card btn-favorite-toggle" data-produto-id="<?= esc($produto['id']) ?>" title="Favoritar Produto" aria-label="Favoritar Produto">
                                    <i class="bi bi-heart"></i>
                                </button>
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
        const formBusca      = document.getElementById('form-busca');
        const inputBusca     = document.getElementById('input-busca');
        const listaProdutos  = document.getElementById('lista-produtos');
        const pagerContainer = document.querySelector('.pagination-container');
        const tituloPagina   = document.getElementById('titulo-pagina');
        const fabBadge       = document.getElementById('filter-count-badge');
        const filterFab      = document.getElementById('filter-fab');

        if (!listaProdutos) return;

        let currentAbortController = null;

        // ----------------------------------------------------------------
        // Helper: Debounce
        // ----------------------------------------------------------------
        function debounce(func, wait = 300) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // ----------------------------------------------------------------
        // Sincronização de Price Sliders e Inputs (Desktop e Mobile)
        // ----------------------------------------------------------------
        function getPriceBounds() {
            const sampleSlider = document.querySelector('.price-range-thumb--min');
            const minBound = sampleSlider ? parseInt(sampleSlider.min) || 0 : 0;
            const maxBound = sampleSlider ? parseInt(sampleSlider.max) || 5000 : 5000;
            return { minBound, maxBound };
        }

        function getPriceValues() {
            const { minBound, maxBound } = getPriceBounds();
            const minSlider = document.querySelector('.price-range-thumb--min');
            const maxSlider = document.querySelector('.price-range-thumb--max');
            const minVal = minSlider ? parseInt(minSlider.value) : minBound;
            const maxVal = maxSlider ? parseInt(maxSlider.value) : maxBound;
            return { minVal, maxVal, minBound, maxBound };
        }

        function updateAllPriceControls(minVal, maxVal) {
            const { minBound, maxBound } = getPriceBounds();
            const clampedMin = Math.max(minBound, Math.min(minVal, maxBound - 50));
            const clampedMax = Math.min(maxBound, Math.max(maxVal, minBound + 50));

            // Atualiza todos os sliders
            document.querySelectorAll('.price-range-thumb--min').forEach(el => el.value = clampedMin);
            document.querySelectorAll('.price-range-thumb--max').forEach(el => el.value = clampedMax);

            // Atualiza todos os inputs numéricos
            document.querySelectorAll('.price-input--min, #price-input-min').forEach(el => el.value = clampedMin);
            document.querySelectorAll('.price-input--max, #price-input-max').forEach(el => el.value = clampedMax);

            // Atualiza as barras visuais (range fill)
            const pctMin = ((clampedMin - minBound) / (maxBound - minBound)) * 100;
            const pctMax = ((clampedMax - minBound) / (maxBound - minBound)) * 100;
            document.querySelectorAll('.price-range-fill, #range-fill').forEach(fill => {
                fill.style.left  = pctMin + '%';
                fill.style.width = (pctMax - pctMin) + '%';
            });

            countActiveFilters();
        }

        // ----------------------------------------------------------------
        // Contagem de filtros ativos
        // ----------------------------------------------------------------
        function countActiveFilters() {
            if (!fabBadge) return;
            const checkedCategories = document.querySelectorAll('.filter-check[name="categorias[]"]:checked:not([value=""])');
            const checkedGenders    = document.querySelectorAll('.filter-check[name="generos[]"]:checked');
            const checkedBrands     = document.querySelectorAll('.filter-check[name="marcas[]"]:checked');

            // Categorias, gêneros e marcas únicas
            const catSet = new Set(Array.from(checkedCategories).map(cb => cb.value));
            const genSet = new Set(Array.from(checkedGenders).map(cb => cb.value));
            const brandSet = new Set(Array.from(checkedBrands).map(cb => cb.value));

            const { minVal, maxVal, minBound, maxBound } = getPriceValues();
            const priceActive = (minVal > minBound) || (maxVal < maxBound);
            const searchActive = inputBusca && inputBusca.value.trim() !== '';

            const total = catSet.size + genSet.size + brandSet.size + (priceActive ? 1 : 0) + (searchActive ? 1 : 0);

            fabBadge.textContent = total;
            fabBadge.classList.toggle('d-none', total === 0);
        }

        // ----------------------------------------------------------------
        // Coleta de filtros e URL
        // ----------------------------------------------------------------
        function coletarFiltros() {
            const params = new URLSearchParams();

            // Termo de busca
            const termo = inputBusca ? inputBusca.value.trim() : '';
            if (termo) params.set('termo', termo);

            // Categorias (checkboxes)
            const catSet = new Set();
            document.querySelectorAll('.filter-check[name="categorias[]"]:checked').forEach(cb => {
                if (cb.value !== '') catSet.add(cb.value);
            });
            catSet.forEach(val => params.append('categorias[]', val));

            // Gêneros
            const genSet = new Set();
            document.querySelectorAll('.filter-check[name="generos[]"]:checked').forEach(cb => {
                genSet.add(cb.value);
            });
            genSet.forEach(val => params.append('generos[]', val));

            // Marcas
            const brandSet = new Set();
            document.querySelectorAll('.filter-check[name="marcas[]"]:checked').forEach(cb => {
                brandSet.add(cb.value);
            });
            brandSet.forEach(val => params.append('marcas[]', val));

            // Faixa de preço
            const { minVal, maxVal, minBound, maxBound } = getPriceValues();
            if (minVal > minBound) {
                params.set('preco_min', minVal);
            }
            if (maxVal < maxBound) {
                params.set('preco_max', maxVal);
            }

            return params;
        }

        // Sincroniza a URL no navegador com os filtros ativos
        function sincronizarUrl(params) {
            const queryString = params.toString();
            const newUrl = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
            const currentSearch = window.location.search.replace(/^\?/, '');

            if (currentSearch !== queryString) {
                window.history.pushState({ filters: queryString }, '', newUrl);
            }
        }

        // Restaura filtros a partir da URL atual
        function restaurarFiltrosDaUrl() {
            const searchParams = new URLSearchParams(window.location.search);
            let hasActiveFilter = false;

            // Busca
            const termo = searchParams.get('termo') || searchParams.get('q') || searchParams.get('busca') || '';
            if (inputBusca) {
                inputBusca.value = termo;
                if (termo) hasActiveFilter = true;
            }

            // Categorias (suporta categorias[], categoria ou categorias)
            const urlCategorias = searchParams.getAll('categorias[]').concat(
                searchParams.getAll('categorias'),
                searchParams.getAll('categoria')
            ).filter(Boolean);

            const hasSpecificCat = urlCategorias.length > 0;
            document.querySelectorAll('.filter-check[name="categorias[]"]').forEach(cb => {
                if (cb.value === '') {
                    cb.checked = !hasSpecificCat;
                } else {
                    cb.checked = urlCategorias.includes(cb.value);
                }
            });
            if (hasSpecificCat) hasActiveFilter = true;

            // Gêneros
            const urlGeneros = searchParams.getAll('generos[]').concat(
                searchParams.getAll('generos'),
                searchParams.getAll('genero')
            ).filter(Boolean);

            document.querySelectorAll('.filter-check[name="generos[]"]').forEach(cb => {
                cb.checked = urlGeneros.includes(cb.value);
            });
            if (urlGeneros.length > 0) hasActiveFilter = true;

            // Marcas
            const urlMarcas = searchParams.getAll('marcas[]').concat(
                searchParams.getAll('marcas'),
                searchParams.getAll('marca')
            ).map(m => m.toLowerCase()).filter(Boolean);

            document.querySelectorAll('.filter-check[name="marcas[]"]').forEach(cb => {
                cb.checked = urlMarcas.includes(cb.value.toLowerCase());
            });
            if (urlMarcas.length > 0) hasActiveFilter = true;

            // Preço
            const { minBound, maxBound } = getPriceBounds();
            const pMin = searchParams.get('preco_min');
            const pMax = searchParams.get('preco_max');

            const finalMin = pMin !== null && !isNaN(parseInt(pMin)) ? parseInt(pMin) : minBound;
            const finalMax = pMax !== null && !isNaN(parseInt(pMax)) ? parseInt(pMax) : maxBound;

            if (finalMin > minBound || finalMax < maxBound) {
                hasActiveFilter = true;
            }

            updateAllPriceControls(finalMin, finalMax);
            countActiveFilters();

            return hasActiveFilter;
        }

        // ----------------------------------------------------------------
        // Requisição AJAX e renderização
        // ----------------------------------------------------------------
        async function aplicarFiltros(options = { pushState: true }) {
            const params = coletarFiltros();

            if (options.pushState) {
                sincronizarUrl(params);
            }

            if (currentAbortController) {
                currentAbortController.abort();
            }
            currentAbortController = new AbortController();

            const url = `<?= site_url('api/produtos/busca') ?>?${params.toString()}`;

            listaProdutos.innerHTML = `
                <div class="col-12 text-center py-5 text-muted">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>
                    <p class="mt-2 mb-0">Atualizando produtos...</p>
                </div>`;
            if (pagerContainer) pagerContainer.style.display = 'none';

            try {
                const response = await fetch(url, { signal: currentAbortController.signal });
                const produtos = await response.json();

                const termo = params.get('termo') ?? '';
                if (tituloPagina) {
                    tituloPagina.textContent = termo
                        ? `Resultados para: "${termo}"`
                        : 'Vitrine de Produtos';
                }

                renderizarProdutos(produtos);
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.error('Erro ao buscar produtos:', err);
                listaProdutos.innerHTML = `<div class="col-12"><div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-exclamation-circle"></i></div>
                    <p class="fw-semibold mb-1">Erro ao carregar produtos</p>
                    <small>Tente novamente em instantes.</small></div></div>`;
            }
        }

        const debouncedAplicarFiltros = debounce(() => aplicarFiltros({ pushState: true }), 300);

        // ----------------------------------------------------------------
        // Renderização dos cards de produto
        // ----------------------------------------------------------------
        function buildImageUrl(imagem) {
            if (!imagem) return '<?= base_url('uploads/produtos/sem_imagem.png') ?>';
            return imagem.startsWith('http')
                ? imagem
                : `<?= base_url('uploads/produtos/') ?>${imagem}`;
        }

        function renderizarProdutos(produtos) {
            listaProdutos.innerHTML = '';

            if (!produtos || produtos.length === 0) {
                listaProdutos.innerHTML = `
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-search"></i></div>
                            <p class="fw-semibold mb-1">Nenhum produto encontrado</p>
                            <small>Tente ajustar os filtros ou o termo de busca.</small>
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
                                <button type="button" class="btn-favorite-card btn-favorite-toggle" data-produto-id="${produto.id}" title="Favoritar Produto" aria-label="Favoritar Produto">
                                    <i class="bi bi-heart"></i>
                                </button>
                                <img src="${imgUrl}" alt="${produto.nome}" loading="lazy">
                                ${badge}
                            </div>
                            <div class="product-card-body">
                                <p class="product-card-category">${produto.categoria_nome ?? ''}</p>
                                <h2 class="product-card-title">${produto.nome}</h2>
                                <p class="product-card-price">${preco}</p>
                            </div>
                            <div class="product-card-footer">
                                <a href="${url}" class="btn-details" id="ver-produto-${produto.id}">
                                    <i class="bi ${icon}"></i> Ver Detalhes
                                </a>
                            </div>
                        </article>
                    </div>`;
            });

            // Re-sync status de favoritos nos novos cards
            try {
                fetch('<?= site_url('api/favoritos/ids') ?>')
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok && Array.isArray(data.ids)) {
                            data.ids.forEach(id => {
                                document.querySelectorAll(`.btn-favorite-toggle[data-produto-id="${id}"]`).forEach(btn => {
                                    btn.classList.add('is-favorite');
                                    const icon = btn.querySelector('i');
                                    if (icon) icon.className = 'bi bi-heart-fill';
                                });
                            });
                        }
                    });
            } catch (e) {}
        }

        // ----------------------------------------------------------------
        // Escuta Automática de Eventos: Checkboxes
        // ----------------------------------------------------------------
        document.querySelectorAll('.filter-check').forEach(cb => {
            cb.addEventListener('change', function () {
                const name  = this.name;
                const value = this.value;
                const isChecked = this.checked;

                // Sincroniza checkbox idêntico no desktop e mobile
                document.querySelectorAll(`.filter-check[name="${name}"][value="${value}"]`).forEach(other => {
                    other.checked = isChecked;
                });

                // Lógica de "Todas" vs Categorias individuais
                if (name === 'categorias[]') {
                    if (value === '') {
                        if (isChecked) {
                            // Desmarca todas as outras categorias
                            document.querySelectorAll('.filter-check[name="categorias[]"]:not([value=""])').forEach(other => {
                                other.checked = false;
                            });
                        }
                    } else {
                        // Se marcou uma categoria específica, desmarca "Todas"
                        if (isChecked) {
                            document.querySelectorAll('.filter-check[name="categorias[]"][value=""]').forEach(allCb => {
                                allCb.checked = false;
                            });
                        } else {
                            // Se desmarcou e nenhuma ficou marcada, marca "Todas"
                            const anyChecked = document.querySelectorAll('.filter-check[name="categorias[]"]:checked:not([value=""])').length > 0;
                            if (!anyChecked) {
                                document.querySelectorAll('.filter-check[name="categorias[]"][value=""]').forEach(allCb => {
                                    allCb.checked = true;
                                });
                            }
                        }
                    }
                }

                countActiveFilters();
                aplicarFiltros({ pushState: true });
            });
        });

        // ----------------------------------------------------------------
        // Escuta com Debounce: Campo de Busca
        // ----------------------------------------------------------------
        if (inputBusca) {
            inputBusca.addEventListener('input', () => {
                countActiveFilters();
                debouncedAplicarFiltros();
            });
        }

        if (formBusca) {
            formBusca.addEventListener('submit', e => {
                e.preventDefault();
                aplicarFiltros({ pushState: true });
            });
        }

        // ----------------------------------------------------------------
        // Escuta com Debounce: Sliders e Inputs de Preço
        // ----------------------------------------------------------------
        document.querySelectorAll('.price-range-thumb--min').forEach(el => {
            el.addEventListener('input', function () {
                const { maxVal } = getPriceValues();
                const vMin = Math.min(parseInt(this.value) || 0, maxVal - 50);
                updateAllPriceControls(vMin, maxVal);
                debouncedAplicarFiltros();
            });
        });

        document.querySelectorAll('.price-range-thumb--max').forEach(el => {
            el.addEventListener('input', function () {
                const { minVal } = getPriceValues();
                const vMax = Math.max(parseInt(this.value) || 5000, minVal + 50);
                updateAllPriceControls(minVal, vMax);
                debouncedAplicarFiltros();
            });
        });

        document.querySelectorAll('.price-input--min, #price-input-min').forEach(el => {
            const handlePriceInput = function () {
                const { minBound, maxBound } = getPriceBounds();
                const { maxVal } = getPriceValues();
                const vMin = Math.max(minBound, Math.min(parseInt(this.value) || minBound, maxVal - 50));
                updateAllPriceControls(vMin, maxVal);
                debouncedAplicarFiltros();
            };
            el.addEventListener('input', handlePriceInput);
            el.addEventListener('change', handlePriceInput);
        });

        document.querySelectorAll('.price-input--max, #price-input-max').forEach(el => {
            const handlePriceInput = function () {
                const { minBound, maxBound } = getPriceBounds();
                const { minVal } = getPriceValues();
                const vMax = Math.min(maxBound, Math.max(parseInt(this.value) || maxBound, minVal + 50));
                updateAllPriceControls(minVal, vMax);
                debouncedAplicarFiltros();
            };
            el.addEventListener('input', handlePriceInput);
            el.addEventListener('change', handlePriceInput);
        });

        // ----------------------------------------------------------------
        // Botão "Limpar tudo"
        // ----------------------------------------------------------------
        document.querySelectorAll('.filter-clear-all, #btn-limpar-filtros').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();

                // Marca "Todas" e desmarca o restante
                document.querySelectorAll('.filter-check').forEach(cb => {
                    cb.checked = (cb.name === 'categorias[]' && cb.value === '');
                });

                // Reseta os valores de preço
                const { minBound, maxBound } = getPriceBounds();
                updateAllPriceControls(minBound, maxBound);

                // Limpa o input de busca
                if (inputBusca) inputBusca.value = '';
                if (tituloPagina) tituloPagina.textContent = 'Vitrine de Produtos';

                // Reseta a URL
                window.history.pushState({}, '', window.location.pathname);

                // Atualiza contagem e lista
                countActiveFilters();
                aplicarFiltros({ pushState: false });
            });
        });

        // Botões "Aplicar Filtros" (desktop e mobile offcanvas)
        ['btn-apply-filters', 'btn-apply-filters-mobile'].forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.addEventListener('click', () => {
                    aplicarFiltros({ pushState: true });
                });
            }
        });

        // ----------------------------------------------------------------
        // Suporte a Navegação de Histórico (Voltar / Avançar no Navegador)
        // ----------------------------------------------------------------
        window.addEventListener('popstate', () => {
            restaurarFiltrosDaUrl();
            aplicarFiltros({ pushState: false });
        });

        // ----------------------------------------------------------------
        // Visibilidade do Botão Flutuante (FAB) no Mobile
        // ----------------------------------------------------------------
        function handleFabVisibility() {
            if (!filterFab || !listaProdutos) return;
            const rect = listaProdutos.getBoundingClientRect();
            filterFab.classList.toggle('filter-fab--visible', rect.top < window.innerHeight && rect.bottom > 0);
        }
        window.addEventListener('scroll', handleFabVisibility, { passive: true });

        // ----------------------------------------------------------------
        // Inicialização
        // ----------------------------------------------------------------
        const { minBound, maxBound } = getPriceBounds();
        updateAllPriceControls(minBound, maxBound);

        const hasUrlFilters = restaurarFiltrosDaUrl();
        if (hasUrlFilters) {
            aplicarFiltros({ pushState: false });
        } else {
            countActiveFilters();
        }

        handleFabVisibility();
    });
</script>
<?= $this->endSection() ?>