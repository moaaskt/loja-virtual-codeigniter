<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/produtos') ?>" class="btn btn-secondary btn-sm mb-3">Voltar para a Lista</a>

    <div class="card">
        <div class="card-body">
            <?php if (!empty(\Config\Services::validation()->getErrors())): ?>
                <div class="alert alert-danger" role="alert">
                    <?= \Config\Services::validation()->listErrors() ?>
                </div>
            <?php endif; ?>

            <?= form_open_multipart('admin/produtos/update/' . $produto['id']) ?>
                <div class="mb-3">
                    <label for="categoria_id" class="form-label">Categoria</label>
                    <select name="categoria_id" id="categoria_id" class="form-select" required>
                        <option value="">Selecione uma categoria...</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= esc($categoria['id']) ?>" <?= old('categoria_id', $produto['categoria_id']) == $categoria['id'] ? 'selected' : '' ?>>
                                <?= esc($categoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Produto</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="<?= old('nome', $produto['nome']) ?>">
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control"><?= old('descricao', $produto['descricao']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="preco" class="form-label">Preço (use ponto para decimais)</label>
                        <input type="number" step="0.01" name="preco" id="preco" class="form-control" value="<?= old('preco', $produto['preco']) ?>" placeholder="19.99">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="estoque" class="form-label">Estoque</label>
                        <input type="number" name="estoque" id="estoque" class="form-control" value="<?= old('estoque', $produto['estoque']) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="imagem" class="form-label">Substituir Imagem</label>
                        <input class="form-control" type="file" id="imagem" name="imagem">
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="form-label">Imagem Atual</label><br>
                        <?php if (!empty($produto['imagem'])): ?>
                            <img src="<?= base_url('uploads/produtos/' . esc($produto['imagem'])) ?>" alt="<?= esc($produto['nome']) ?>" height="100" class="img-thumbnail">
                        <?php else: ?>
                            <p class="text-muted">Nenhuma imagem cadastrada.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Atualizar Produto</button>
                <a href="<?= site_url('admin/produtos') ?>" class="btn btn-light">Cancelar</a>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>