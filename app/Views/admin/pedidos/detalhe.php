<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/pedidos') ?>" class="btn btn-secondary btn-sm mb-3">Voltar para a Lista</a>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Produtos do Pedido</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach($produtos as $produto): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url('uploads/produtos/' . esc($produto['imagem'])) ?>" alt="<?= esc($produto['nome']) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                                    <div>
                                        <?= esc($produto['nome']) ?><br>
                                        <small class="text-muted">Qtd: <?= esc($produto['quantidade']) ?> | Preço Unit.: R$ <?= esc(number_format($produto['preco_unitario'], 2, ',', '.')) ?></small>
                                    </div>
                                </div>
                                <strong>R$ <?= esc(number_format($produto['preco_unitario'] * $produto['quantidade'], 2, ',', '.')) ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Detalhes do Cliente e Pedido</h5>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> <?= esc($pedido['cliente_nome']) ?></p>
                    <p><strong>Email:</strong> <?= esc($pedido['cliente_email']) ?></p>
                    <p><strong>Data:</strong> <?= esc(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></p>
                    <p><strong>Valor Total:</strong> <span class="fs-5 text-success">R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?></span></p>
                    <hr>
                    <p><strong>Atualizar Status:</strong></p>
                    <?= form_open('admin/pedidos/atualizar-status/' . $pedido['id']) ?>
                        <div class="input-group">
                            <select name="status" class="form-select">
                                <?php foreach ($status_options as $status): ?>
                                    <option value="<?= $status ?>" <?= ($pedido['status'] === $status) ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>