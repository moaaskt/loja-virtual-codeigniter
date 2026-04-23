<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
        <a href="<?= site_url('admin/clientes') ?>" class="btn btn-secondary btn-sm">Voltar para Clientes</a>
    </div>

    <div class="row">
        <!-- Customer Info -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Informações do Cliente</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong> <?= esc($cliente['id']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Nome:</strong> <?= esc($cliente['nome']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>E-mail:</strong> <?= esc($cliente['email']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Data de Cadastro:</strong> <?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> 
                        <?php if ($cliente['ativo']): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="col-md-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Histórico de Compras</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($pedidos)): ?>
                        <div class="alert alert-info">Nenhum pedido encontrado para este cliente.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pedido #</th>
                                        <th>Data</th>
                                        <th>Valor Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td><?= esc($pedido['id']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                                            <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                                            <td>
                                                <?php
                                                    $statusClass = 'bg-secondary';
                                                    if ($pedido['status'] === 'pago') $statusClass = 'bg-success';
                                                    elseif ($pedido['status'] === 'cancelado') $statusClass = 'bg-danger';
                                                    elseif ($pedido['status'] === 'pendente') $statusClass = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= esc(ucfirst($pedido['status'])) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('admin/pedidos/detalhe/' . $pedido['id']) ?>" class="btn btn-outline-primary btn-sm">Ver Pedido</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
