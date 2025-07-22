<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h3 class="mb-3">Categorias</h3>
                <div class="list-group shadow-sm">
                    <a href="<?= site_url('/') ?>" class="list-group-item list-group-item-action <?= (empty($categoriaAtivaId)) ? 'active' : '' ?>">
                        Todas
                    </a>
                    <?php foreach ($categorias as $categoria): ?>
                        <a href="<?= site_url('categoria/' . $categoria['id']) ?>" 
                           class="list-group-item list-group-item-action <?= (!empty($categoriaAtivaId) && $categoriaAtivaId == $categoria['id']) ? 'active' : '' ?>">
                            <?= esc($categoria['nome']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-9">
                <h1 class="mb-4"><?= esc($title) ?></h1>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4" id="lista-produtos">
                    <?php if (!empty($produtos) && is_array($produtos)): ?>
                       <?php foreach ($produtos as $produto): ?>
                           <div class="col">
                               <div class="card h-100 shadow-sm">
                                   <?php if (!empty($produto['imagem'])): ?>
                                       <img src="<?= base_url('uploads/produtos/' . esc($produto['imagem'])) ?>" class="card-img-top" alt="<?= esc($produto['nome']) ?>" style="height: 200px; object-fit: cover;">
                                   <?php else: ?>
                                       <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>" class="card-img-top" alt="Sem Imagem" style="height: 200px; object-fit: cover;">
                                   <?php endif; ?>

                                   <div class="card-body">
                                       <h5 class="card-title"><?= esc($produto['nome']) ?></h5>
                                       <p class="card-text text-muted"><?= esc($produto['categoria_nome']) ?></p>
                                       <h4 class="card-text text-success">R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></h4>
                                   </div>
                                   <div class="card-footer text-center">
                                       <a href="<?= site_url('produto/' . $produto['id']) ?>" class="btn btn-primary">Ver Detalhes</a>
                                   </div>
                               </div>
                           </div>
                       <?php endforeach; ?>
                    <?php else: ?>
                       <div class="col">
                           <p>Nenhum produto encontrado.</p>
                       </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pagination-container">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const formBusca = document.getElementById('form-busca');
        const inputBusca = formBusca.querySelector('input[name="termo"]');
        const listaProdutos = document.getElementById('lista-produtos');
        const pagerContainer = document.querySelector('.pagination-container');
        const tituloPagina = document.querySelector('.col-md-9 h1');
        const tituloOriginal = tituloPagina.textContent;

        if (!formBusca || !inputBusca || !listaProdutos) {
            console.error('Elementos essenciais da busca não foram encontrados.');
            return;
        }

        let debounceTimer;

        inputBusca.addEventListener('keyup', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const termo = e.target.value.trim();
                buscarProdutos(termo);
            }, 300);
        });
        
        formBusca.addEventListener('submit', (e) => {
            e.preventDefault();
            buscarProdutos(inputBusca.value.trim());
        });

        async function buscarProdutos(termo) {
            try {
                // Se o termo estiver vazio, redireciona para a página inicial
                if (termo.length === 0) {
                    window.location.href = '<?= site_url('/') ?>';
                    return;
                }

                const url = `<?= site_url('api/produtos/busca') ?>?termo=${encodeURIComponent(termo)}`;
                const response = await fetch(url);
                const produtos = await response.json();
                renderizarProdutos(produtos, termo);
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
            }
        }
        
        function renderizarProdutos(produtos, termo) {
            listaProdutos.innerHTML = '';
            tituloPagina.textContent = `Resultados da busca por: "${termo}"`;
            
            if (pagerContainer) {
                pagerContainer.style.display = 'none';
            }

            if (produtos.length === 0) {
                listaProdutos.innerHTML = '<div class="col"><p>Nenhum produto encontrado para sua busca.</p></div>';
                return;
            }

            produtos.forEach(produto => {
                const precoFormatado = parseFloat(produto.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const imagemUrl = `<?= base_url('uploads/produtos/') ?>${produto.imagem || 'sem_imagem.png'}`;
                const produtoUrl = `<?= site_url('produto/') ?>${produto.id}`;

                const cardHtml = `
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="${imagemUrl}" class="card-img-top" alt="${produto.nome}" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">${produto.nome}</h5>
                                <p class="card-text text-muted">${produto.categoria_nome}</p>
                                <h4 class="card-text text-success">${precoFormatado}</h4>
                            </div>
                            <div class="card-footer text-center">
                                <a href="${produtoUrl}" class="btn btn-primary">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                `;
                listaProdutos.innerHTML += cardHtml;
            });
        }
    });
</script>
<?= $this->endSection() ?>