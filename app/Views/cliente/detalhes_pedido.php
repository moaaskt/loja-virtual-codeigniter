<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('minha-conta/pedidos') ?>">Meus Pedidos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pedido #<?= esc($pedido['id']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-12 col-lg-4 col-xl-3">
            <?= $this->include('cliente/_sidebar') ?>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-12 col-lg-8 col-xl-9">

            <!-- Cabeçalho do Pedido -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2">
                                Pedido Confirmado
                            </span>
                            <h1 class="fs-4 fw-bold mb-1 text-dark">
                                Pedido #<?= esc($pedido['id']) ?>
                            </h1>
                            <p class="text-muted small mb-0">
                                Realizado em <strong><?= date('d/m/Y \à\s H:i', strtotime($pedido['criado_em'])) ?></strong>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="<?= site_url('minha-conta/pedidos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-arrow-left me-1"></i>Voltar aos Pedidos
                            </a>
                        </div>
                    </div>

                    <!-- ===== TIMELINE VISUAL DE RASTREIO DE STATUS ===== -->
                    <div class="pdp-order-timeline py-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="fs-6 fw-bold mb-0 text-dark">
                                <i class="bi bi-clock-history text-primary me-2"></i>Status do Pedido:
                                <span class="badge <?= esc($timeline['badge_class'] ?? 'bg-secondary') ?> ms-1">
                                    <?= esc($timeline['status_label'] ?? ucfirst($pedido['status'])) ?>
                                </span>
                            </h2>
                            <?php if (!empty($pedido['codigo_rastreio'])): ?>
                                <span class="badge bg-light text-dark border font-monospace px-3 py-2">
                                    <i class="bi bi-truck text-primary me-1"></i>Rastreio: <?= esc($pedido['codigo_rastreio']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($timeline['is_cancelado'])): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-3 p-3 rounded-4 mb-0" role="alert">
                                <i class="bi bi-x-circle-fill fs-2"></i>
                                <div>
                                    <h3 class="fs-6 fw-bold mb-1">Este pedido foi cancelado</h3>
                                    <p class="small mb-0 opacity-90">Em caso de dúvidas sobre estornos ou pagamentos, entre em contato com nosso atendimento.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Linha do Tempo de 5 Etapas -->
                            <div class="timeline-stepper d-flex align-items-start justify-content-between position-relative pt-3 pb-2">
                                <?php foreach ($timeline['etapas'] as $idx => $etapa): ?>
                                    <?php 
                                    $isConcluido = !empty($etapa['concluido']);
                                    $isAtivo = !empty($etapa['ativo']);
                                    ?>
                                    <div class="timeline-step text-center position-relative flex-grow-1">
                                        <div class="step-icon-wrap mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle shadow-xs <?= $isConcluido ? 'bg-primary text-white' : 'bg-light text-muted border' ?> <?= $isAtivo ? 'ring-active' : '' ?>"
                                             style="width: 44px; height: 44px; font-size: 1.2rem; transition: all 0.3s ease;">
                                            <i class="bi <?= esc($etapa['icone']) ?>"></i>
                                        </div>
                                        <div class="step-text">
                                            <strong class="d-block small <?= $isConcluido ? 'text-dark' : 'text-muted' ?>" style="font-size: 0.8125rem;">
                                                <?= esc($etapa['titulo']) ?>
                                            </strong>
                                            <span class="text-muted d-none d-md-block" style="font-size: 0.7rem;">
                                                <?= esc($etapa['subtitulo']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Detalhes dos Itens do Pedido -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-light p-3 border-bottom">
                    <h2 class="fs-6 fw-bold mb-0 text-dark">
                        <i class="bi bi-box-seam text-primary me-2"></i>Itens do Pedido (<?= count($itens) ?>)
                    </h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-4" style="min-width: 260px;">Produto</th>
                                    <th class="text-center">Preço Unit.</th>
                                    <th class="text-center">Qtd.</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itens as $item): ?>
                                    <?php 
                                    $imgUrl = !empty($item['imagem'])
                                        ? (strpos($item['imagem'], 'http') === 0 ? esc($item['imagem']) : base_url('uploads/produtos/' . esc($item['imagem'])))
                                        : base_url('uploads/produtos/sem_imagem.png');
                                    ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= $imgUrl ?>" alt="<?= esc($item['nome']) ?>"
                                                     class="rounded-3 border object-fit-cover shadow-xs flex-shrink-0"
                                                     style="width: 64px; height: 64px;">
                                                <div>
                                                    <a href="<?= site_url('produto/' . $item['produto_id']) ?>" class="text-decoration-none text-dark fw-bold d-block mb-1">
                                                        <?= esc($item['nome']) ?>
                                                    </a>
                                                    
                                                    <?php if (!empty($item['sku'])): ?>
                                                        <span class="badge bg-light text-secondary border font-monospace me-1" style="font-size:0.7rem;">
                                                            SKU: <?= esc($item['sku']) ?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if (!empty($item['nome_variacao'])): ?>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:0.7rem;">
                                                            <?= esc($item['nome_variacao']) ?>
                                                        </span>
                                                    <?php elseif (!empty($item['tamanho']) || !empty($item['cor'])): ?>
                                                        <span class="badge bg-light text-dark border" style="font-size:0.7rem;">
                                                            <?= esc($item['tamanho'] ?? '') ?><?= !empty($item['tamanho']) && !empty($item['cor']) ? ' / ' : '' ?><?= esc($item['cor'] ?? '') ?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if (in_array(strtolower($pedido['status']), ['pago', 'enviado', 'entregue', 'processando']) || in_array(strtolower($pedido['status_pagamento'] ?? ''), ['pago', 'aprovado'])): ?>
                                                        <div class="mt-2">
                                                            <a href="<?= site_url('produto/' . $item['produto_id'] . '#secao-avaliacoes') ?>"
                                                               class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 py-0 d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 0.75rem;">
                                                                <i class="bi bi-star-fill text-warning"></i> Avaliar Produto
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace small">
                                            R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?>
                                        </td>
                                        <td class="text-center fw-bold small">
                                            <?= (int)$item['quantidade'] ?>
                                        </td>
                                        <td class="text-end pe-4 font-monospace fw-bold text-dark">
                                            R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Resumo Financeiro & Entrega (2 colunas) -->
            <div class="row g-4">
                <!-- Endereço de Entrega -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                        <h2 class="fs-6 fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-primary"></i>Endereço de Entrega
                        </h2>
                        <div class="text-secondary small lh-lg">
                            <?php if (!empty($pedido['logradouro'])): ?>
                                <p class="mb-1 fw-semibold text-dark">
                                    <?= esc($pedido['logradouro']) ?>, <?= esc($pedido['numero']) ?>
                                    <?= !empty($pedido['complemento']) ? ' - ' . esc($pedido['complemento']) : '' ?>
                                </p>
                                <p class="mb-1"><?= esc($pedido['bairro']) ?> — <?= esc($pedido['cidade']) ?>/<?= esc($pedido['uf']) ?></p>
                                <p class="mb-2 font-monospace"><i class="bi bi-mailbox me-1"></i>CEP: <?= esc($pedido['cep']) ?></p>
                            <?php else: ?>
                                <p class="text-muted italic">Endereço não informado.</p>
                            <?php endif; ?>

                            <?php if (!empty($pedido['frete_modalidade'])): ?>
                                <div class="mt-2 pt-2 border-top">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-truck text-primary me-1"></i>Modalidade: <?= esc($pedido['frete_modalidade']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Resumo do Pagamento -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                        <h2 class="fs-6 fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card-2-front-fill text-primary"></i>Resumo do Pagamento
                        </h2>
                        <div class="small text-secondary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Método de Pagamento:</span>
                                <strong>
                                    <i class="bi <?= ($pedido['forma_pagamento'] === 'pix') ? 'bi-qr-code text-success' : 'bi-credit-card text-primary' ?> me-1"></i>
                                    <?= esc(getMetodoPagamentoLabel($pedido['forma_pagamento'])) ?>
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Status do Pagamento:</span>
                                <span class="badge <?= getPagamentoStatusColorClass($pedido['status_pagamento'] ?? $pedido['status']) ?>">
                                    <?= esc(ucfirst($pedido['status_pagamento'] ?? $pedido['status'])) ?>
                                </span>
                            </div>

                            <?php if (!empty($pedido['cupom_codigo'])): ?>
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Cupom (<?= esc($pedido['cupom_codigo']) ?>):</span>
                                    <span>- R$ <?= number_format((float)$pedido['desconto_valor'], 2, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($pedido['frete_valor'])): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Frete:</span>
                                    <span><?= (float)$pedido['frete_valor'] === 0.0 ? '<strong class="text-success">GRÁTIS</strong>' : 'R$ ' . number_format((float)$pedido['frete_valor'], 2, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between pt-2 border-top mt-2 fs-6">
                                <strong class="text-dark">Total Pago:</strong>
                                <strong class="text-success fs-5 font-monospace">R$ <?= number_format((float)$pedido['valor_total'], 2, ',', '.') ?></strong>
                            </div>

                            <?php if (($pedido['forma_pagamento'] ?? '') === 'pix' && in_array(strtolower($pedido['status']), ['pendente', 'aguardando_pagamento'])): ?>
                                <div class="mt-3 text-center">
                                    <a href="<?= site_url('pedido/pagamento/' . $pedido['id']) ?>" class="btn btn-success btn-sm rounded-pill w-100 fw-semibold shadow-sm">
                                        <i class="bi bi-qr-code me-1"></i>Abrir QR Code / Copiar Código Pix
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Estilos da Timeline de Pedidos */
.timeline-stepper::before {
    content: '';
    position: absolute;
    top: 35px;
    left: 8%;
    right: 8%;
    height: 4px;
    background: #e2e8f0;
    z-index: 1;
}
.timeline-step {
    z-index: 2;
}
.step-icon-wrap {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.step-icon-wrap.ring-active {
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
    transform: scale(1.08);
}
</style>
<?= $this->endSection() ?>
