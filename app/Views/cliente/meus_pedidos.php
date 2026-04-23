<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1><?= esc($title) ?></h1>
    <p>Aqui está o histórico de todos os seus pedidos.</p>

    <?php if (!empty($pedidos)): ?>
        <div class="accordion" id="accordionPedidos">
            <?php foreach ($pedidos as $pedido): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $pedido['id'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse<?= $pedido['id'] ?>" aria-expanded="false"
                            aria-controls="collapse<?= $pedido['id'] ?>">
                            <div class="d-flex justify-content-between w-100 pe-3">
                                <span><strong>Pedido #<?= esc($pedido['id']) ?></strong></span>
                                <span>Data: <?= esc(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></span>
                                <span>Total: R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?></span>
                                <span>Status: <span class="badge <?= getStatusColorClass($pedido['status']) ?>"><?= esc(ucfirst($pedido['status'])) ?></span></span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse<?= $pedido['id'] ?>" class="accordion-collapse collapse"
                        aria-labelledby="heading<?= $pedido['id'] ?>" data-bs-parent="#accordionPedidos">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <?php if (isset($itens_dos_pedidos[$pedido['id']])): ?>
                                    <?php foreach ($itens_dos_pedidos[$pedido['id']] as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= strpos($item['imagem'], 'http') === 0 ? esc($item['imagem']) : base_url('uploads/produtos/' . esc($item['imagem'])) ?>"
                                                    alt="<?= esc($item['nome']) ?>"
                                                    style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                                                <div>
                                                    <span class="d-block fw-semibold"><?= esc($item['nome']) ?></span>
                                                    <?php if (!empty($item['tamanho']) || !empty($item['cor'])): ?>
                                                        <small class="text-muted d-block mt-1">
                                                            <?= !empty($item['tamanho']) ? 'Tamanho: ' . esc($item['tamanho']) : '' ?>
                                                            <?= !empty($item['tamanho']) && !empty($item['cor']) ? ' | ' : '' ?>
                                                            <?= !empty($item['cor']) ? 'Cor: ' . esc($item['cor']) : '' ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <small class="text-muted d-block">Quantidade: <?= esc($item['quantidade']) ?></small>
                                                </div>
                                            </div>
                                            <span class="badge bg-secondary rounded-pill">
                                                R$ <?= esc(number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.')) ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Você ainda não fez nenhum pedido.</div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>