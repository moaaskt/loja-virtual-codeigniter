<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <?php if (!empty($produto['imagem'])): ?>
                <img src="<?= base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                    class="img-fluid rounded shadow-sm" alt="<?= esc($produto['nome']) ?>">
            <?php else: ?>
                <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>" class="img-fluid rounded shadow-sm"
                    alt="Sem Imagem">
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h2><?= esc($produto['nome']) ?></h2>
            <p class="text-muted">Categoria: <?= esc($produto['categoria_nome']) ?></p>

            <h3 class="text-success my-4">R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></h3>

            <h5>Descrição</h5>
            <p><?= esc($produto['descricao']) ?></p>

            <p><strong>Em estoque:</strong> <?= esc($produto['estoque']) ?> unidades</p>

            <div class="mt-4">
                <?= form_open('carrinho/adicionar') ?>
                <input type="hidden" name="produto_id" value="<?= esc($produto['id']) ?>">

                <div class="row">
                    <div class="col-md-4">
                        <label for="quantidade" class="form-label">Quantidade:</label>
                        <input type="number" name="quantidade" id="quantidade" class="form-control" value="1" min="1"
                            max="<?= esc($produto['estoque']) ?>">
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success btn-lg">Adicionar ao Carrinho</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>