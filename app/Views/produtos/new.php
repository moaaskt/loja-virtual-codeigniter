<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<?php if (!empty(\Config\Services::validation()->getErrors())): ?>
    <div class="alert alert-danger" role="alert">
        <?= \Config\Services::validation()->listErrors() ?>
    </div>
<?php endif; ?>

<?= form_open_multipart('admin/produtos/create') ?>

<div class="mb-3">
    <label for="categoria_id" class="form-label">Categoria</label>
    <select name="categoria_id" id="categoria_id" class="form-select" required>
        <option value="">Selecione uma categoria...</option>
        <?php foreach ($categorias as $categoria): ?>
            <option value="<?= esc($categoria['id']) ?>" <?= old('categoria_id') == $categoria['id'] ? 'selected' : '' ?>>
                <?= esc($categoria['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label for="nome" class="form-label">Nome do Produto</label>
    <input type="text" name="nome" id="nome" class="form-control" value="<?= old('nome') ?>">
</div>

<div class="mb-3">
    <label for="descricao" class="form-label">Descrição</label>
    <textarea name="descricao" id="descricao" class="form-control"><?= old('descricao') ?></textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="preco" class="form-label">Preço (use ponto para decimais)</label>
        <input type="number" step="0.01" name="preco" id="preco" class="form-control" value="<?= old('preco') ?>"
            placeholder="19.99">
    </div>
    <div class="col-md-6 mb-3">
        <label for="estoque" class="form-label">Estoque</label>
        <input type="number" name="estoque" id="estoque" class="form-control" value="<?= old('estoque') ?>">
    </div>
</div>
<div class="mb-3">
    <label for="imagem" class="form-label">Imagem do Produto</label>
    <input class="form-control" type="file" id="imagem" name="imagem">
</div>
<button type="submit" class="btn btn-success">Salvar Produto</button>
<a href="<?= site_url('admin/produtos') ?>" class="btn btn-secondary">Cancelar</a>

<?= form_close() ?>

<?= $this->endSection() ?>