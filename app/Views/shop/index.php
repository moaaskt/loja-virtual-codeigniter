<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row g-4">

    <!-- ===== CATEGORY SIDEBAR ===== -->
    <div class="col-lg-2 col-md-3">
        <p class="category-section-title">Categorias</p>
        <ul class="category-list">
            <li>
                <a href="<?= site_url('/') ?>" class="<?= (empty($categoriaAtivaId)) ? 'active' : '' ?>"
                    id="cat-todas">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Todas
                </a>
            </li>
            <?php foreach ($categorias as $categoria): ?>
                <li>
                    <a href="<?= site_url('categoria/' . $categoria['id']) ?>"
                        id="cat-<?= esc($categoria['id']) ?>"
                        class="<?= (!empty($categoriaAtivaId) && $categoriaAtivaId == $categoria['id']) ? 'active' : '' ?>">
                        <?= esc($categoria['nome']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- ===== PRODUCT GRID ===== -->
    <div class="col-lg-10 col-md-9">

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
<?= $this->endSection() ?>