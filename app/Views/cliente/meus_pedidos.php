<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minha Conta / Meus Pedidos</li>
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
                        <i class="bi bi-bag-check-fill text-primary me-2"></i>Meus Pedidos
                    </h1>
                    <p class="text-muted small mb-0">Acompanhe seus pedidos, status de entrega e histórico de compras.</p>
                </div>
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

            <?php if (!empty($pedidos)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php 
                        $statusClass = getStatusColorClass($pedido['status']);
                        $itens = $itens_dos_pedidos[$pedido['id']] ?? [];
                        ?>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden order-card">
                            <!-- Card Header -->
                            <div class="card-header bg-light bg-opacity-75 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        <span class="text-muted small d-block">Número do Pedido</span>
                                        <strong class="text-dark fs-6">#<?= esc($pedido['id']) ?></strong>
                                    </div>
                                    <div class="border-start ps-3 d-none d-sm-block">
                                        <span class="text-muted small d-block">Data da Compra</span>
                                        <span class="text-secondary small fw-medium"><?= date('d/m/Y \à\s H:i', strtotime($pedido['criado_em'])) ?></span>
                                    </div>
                                    <div class="border-start ps-3 d-none d-md-block">
                                        <span class="text-muted small d-block">Pagamento</span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi <?= ($pedido['forma_pagamento'] === 'pix') ? 'bi-qr-code text-success' : 'bi-credit-card text-primary' ?> me-1"></i>
                                            <?= esc(getMetodoPagamentoLabel($pedido['forma_pagamento'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge <?= $statusClass ?> px-3 py-2 rounded-pill shadow-xs text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <?= esc(ucfirst($pedido['status'])) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body: Itens Preview -->
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3">
                                    <div class="col-12 col-md-7">
                                        <div class="d-flex align-items-center gap-2 overflow-auto pb-1">
                                            <?php foreach (array_slice($itens, 0, 4) as $item): ?>
                                                <?php 
                                                $imgUrl = !empty($item['imagem'])
                                                    ? (strpos($item['imagem'], 'http') === 0 ? esc($item['imagem']) : base_url('uploads/produtos/' . esc($item['imagem'])))
                                                    : base_url('uploads/produtos/sem_imagem.png');
                                                ?>
                                                <div class="position-relative flex-shrink-0" title="<?= esc($item['nome']) ?>">
                                                    <img src="<?= $imgUrl ?>" alt="<?= esc($item['nome']) ?>"
                                                         class="rounded-3 border object-fit-cover shadow-xs"
                                                         style="width: 56px; height: 56px;">
                                                    <?php if ((int)$item['quantidade'] > 1): ?>
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark text-white shadow-xs" style="font-size: 0.65rem;">
                                                            <?= (int)$item['quantidade'] ?>x
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                            <?php if (count($itens) > 4): ?>
                                                <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center text-muted fw-bold small flex-shrink-0"
                                                     style="width: 56px; height: 56px;">
                                                    +<?= count($itens) - 4 ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="ms-2 small text-muted">
                                                <span><?= count($itens) ?> <?= count($itens) === 1 ? 'item' : 'itens' ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-5 text-md-end">
                                        <div class="d-flex flex-column align-items-md-end justify-content-center">
                                            <span class="text-muted small">Valor Total</span>
                                            <strong class="text-success fs-5">R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Actions -->
                            <div class="card-footer bg-white p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <?php if (!empty($pedido['codigo_rastreio'])): ?>
                                        <span class="small text-muted">
                                            <i class="bi bi-truck text-primary me-1"></i>Rastreio: 
                                            <strong class="font-monospace text-dark"><?= esc($pedido['codigo_rastreio']) ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (($pedido['forma_pagamento'] ?? '') === 'pix' && in_array(strtolower($pedido['status']), ['pendente', 'aguardando_pagamento'])): ?>
                                        <a href="<?= site_url('pedido/pagamento/' . $pedido['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold shadow-xs">
                                            <i class="bi bi-qr-code me-1"></i>Pagar Pix
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= site_url('minha-conta/pedidos/' . $pedido['id']) ?>"
                                       class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-xs">
                                        <i class="bi bi-eye-fill me-1"></i>Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="card border-0 bg-light p-5 text-center rounded-4 shadow-sm">
                    <div class="py-4">
                        <i class="bi bi-bag-x text-muted mb-3 d-block" style="font-size: 3.5rem;"></i>
                        <h3 class="fs-5 fw-bold text-dark mb-2">Você ainda não realizou nenhum pedido</h3>
                        <p class="text-muted small mb-4">Explore nossa loja e encontre os melhores produtos com frete rápido e condições especiais.</p>
                        <a href="<?= site_url('/') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-shop me-2"></i>Ir para a Vitrine
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?= $this->endSection() ?>