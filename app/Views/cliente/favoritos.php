<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minha Conta / Lista de Desejos</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-12 col-lg-4 col-xl-3">
            <?= $this->include('cliente/_sidebar') ?>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-12 col-lg-8 col-xl-9">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h1 class="fs-4 fw-bold mb-1 text-dark">
                        <i class="bi bi-heart-fill text-danger me-2"></i>Minha Lista de Desejos
                    </h1>
                    <p class="text-muted small mb-0">Produtos que você salvou para comprar mais tarde.</p>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-semibold">
                    <?= count($favoritos) ?> <?= count($favoritos) === 1 ? 'produto salvo' : 'produtos salvos' ?>
                </span>
            </div>

            <!-- Feedback Messages -->
            <?php if (session()->getFlashdata('sucesso')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('sucesso') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('erro') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Grid de Produtos Favoritados -->
            <?php if (!empty($favoritos)): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3" id="grid-favoritos">
                    <?php foreach ($favoritos as $prod): ?>
                        <?php 
                        $esgotado = (int)$prod['estoque'] === 0;
                        $imgUrl = !empty($prod['imagem'])
                            ? (strpos($prod['imagem'], 'http') === 0 ? esc($prod['imagem']) : base_url('uploads/produtos/' . esc($prod['imagem'])))
                            : base_url('uploads/produtos/sem_imagem.png');
                        ?>
                        <div class="col d-flex align-items-stretch">
                            <div class="card border-0 shadow-sm rounded-4 w-100 p-3 bg-white position-relative d-flex flex-column <?= $esgotado ? 'opacity-75' : '' ?>">
                                
                                <!-- Imagem e Botão Remover -->
                                <div class="position-relative mb-3 overflow-hidden rounded-3 bg-light text-center" style="height: 180px;">
                                    <a href="<?= site_url('produto/' . $prod['id']) ?>">
                                        <img src="<?= $imgUrl ?>" alt="<?= esc($prod['nome']) ?>"
                                             class="w-100 h-100 object-fit-contain p-2 transition-transform">
                                    </a>

                                    <!-- Botão Excluir Favorito -->
                                    <?= form_open('minha-conta/favoritos/remover/' . $prod['id'], ['class' => 'position-absolute top-0 end-0 m-2']) ?>
                                        <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm p-2"
                                                title="Remover dos Favoritos" aria-label="Remover">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    <?= form_close() ?>

                                    <?php if ($esgotado): ?>
                                        <span class="badge bg-dark position-absolute bottom-0 start-0 m-2 px-2 py-1" style="font-size: 0.7rem;">
                                            Esgotado
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Info -->
                                <div class="flex-grow-1 d-flex flex-column">
                                    <span class="text-muted small d-block mb-1"><?= esc($prod['categoria_nome'] ?? 'Geral') ?></span>
                                    <h2 class="fs-6 fw-bold mb-2">
                                        <a href="<?= site_url('produto/' . $prod['id']) ?>" class="text-decoration-none text-dark">
                                            <?= esc($prod['nome']) ?>
                                        </a>
                                    </h2>

                                    <div class="mt-auto pt-2">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <strong class="text-success fs-5">
                                                R$ <?= number_format((float)$prod['preco'], 2, ',', '.') ?>
                                            </strong>
                                            <?php if (!$esgotado): ?>
                                                <small class="text-muted" style="font-size:0.75rem;">
                                                    <i class="bi bi-check-circle-fill text-success me-1"></i>Em estoque
                                                </small>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!$esgotado): ?>
                                            <?= form_open('carrinho/adicionar') ?>
                                                <input type="hidden" name="produto_id" value="<?= esc($prod['id']) ?>">
                                                <input type="hidden" name="quantidade" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-semibold shadow-xs">
                                                    <i class="bi bi-bag-plus-fill me-1"></i>Adicionar ao Carrinho
                                                </button>
                                            <?= form_close() ?>
                                        <?php else: ?>
                                            <a href="<?= site_url('produto/' . $prod['id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill w-100 fw-semibold">
                                                Ver Detalhes
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="card border-0 bg-light p-5 text-center rounded-4 shadow-sm">
                    <div class="py-4">
                        <i class="bi bi-heartbreak text-muted mb-3 d-block" style="font-size: 3.5rem;"></i>
                        <h3 class="fs-5 fw-bold text-dark mb-2">Sua Lista de Desejos está vazia</h3>
                        <p class="text-muted small mb-4">Você ainda não favoritou nenhum produto. Navegue pela loja e clique no ícone de coração para salvar itens!</p>
                        <a href="<?= site_url('/') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-shop me-2"></i>Explorar Produtos
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
