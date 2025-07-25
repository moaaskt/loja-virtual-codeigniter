<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <h1><?= esc($title) ?></h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <p>
        <a href="<?= site_url('admin/produtos/new') ?>" class="btn btn-success mb-3">Adicionar Novo Produto</a>
    </p>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
               <th>Imagem</th> <th>Nome do Produto</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produtos) && is_array($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= esc($produto['id']) ?></td>
                         <td>
                <?php if (!empty($produto['imagem'])): ?>
                    <img src="<?= base_url('uploads/produtos/' . esc($produto['imagem'])) ?>" alt="<?= esc($produto['nome']) ?>" width="80">
                <?php else: ?>
                    <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>" alt="Sem Imagem" width="80"> <?php endif; ?>
            </td>
                        <td><?= esc($produto['nome']) ?></td>
                        <td><?= esc($produto['categoria_nome']) ?></td>
                        <td>R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></td>
                        <td><?= esc($produto['estoque']) ?></td>
                        <td class="d-flex" style="gap: 5px;">
                            <a href="<?= site_url('admin/produtos/edit/' . $produto['id']) ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form action="<?= site_url('admin/produtos/delete/' . $produto['id']) ?>" method="post" class="d-inline">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?');">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Nenhum produto encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <?= $pager->links() ?>
    </div>

<?= $this->endSection() ?>