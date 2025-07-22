<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container">
    <h1 class="mt-4 mb-4"><?= esc($title) ?></h1>

    <?php if (empty($carrinho)): ?>
        <div class="alert alert-info" role="alert">
            Seu carrinho de compras está vazio.
        </div>
        <a href="<?= site_url('/') ?>" class="btn btn-primary">Continuar Comprando</a>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Imagem</th>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($carrinho as $id => $item):
                    $subtotal = $item['preco'] * $item['quantidade'];
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="<?= base_url('uploads/produtos/' . esc($item['imagem'])) ?>"
                                alt="<?= esc($item['nome']) ?>" width="80">
                        </td>
                        <td><?= esc($item['nome']) ?></td>
                        <td>R$ <?= esc(number_format($item['preco'], 2, ',', '.')) ?></td>
                        <td>
                            <?= form_open('carrinho/atualizar', ['class' => 'd-flex']) ?>
                            <input type="hidden" name="produto_id" value="<?= $id ?>">
                            <input type="number" name="quantidade" class="form-control form-control-sm"
                                value="<?= esc($item['quantidade']) ?>" min="1" style="width: 70px;">
                            <button type="submit" class="btn btn-primary btn-sm ms-2">Atualizar</button>
                            <?= form_close() ?>
                        </td>
                        <td>R$ <?= esc(number_format($subtotal, 2, ',', '.')) ?></td>
                        <td>
                            <?= form_open('carrinho/remover/' . $id, ['class' => 'd-inline']) ?>
                            <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                            <?= form_close() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong>R$ <?= esc(number_format($total, 2, ',', '.')) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="<?= site_url('/') ?>" class="btn btn-secondary">Continuar Comprando</a>
            <?= form_open('checkout/finalizar') ?>
            <button type="submit" class="btn btn-success">Finalizar Compra</button>
            <?= form_close() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>