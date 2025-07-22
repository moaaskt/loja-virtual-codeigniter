<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
</head>

<body>

    <h1><?= esc($title) ?></h1>

    <p><a href="<?= site_url('categorias/new') ?>">Adicionar Nova Categoria</a></p>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= esc($categoria['id']) ?></td>
                        <td><?= esc($categoria['nome']) ?></td>
                        <td>
                            <a href="<?= site_url('categorias/edit/' . $categoria['id']) ?>">Editar</a>
                            <form action="<?= site_url('categorias/delete/' . $categoria['id']) ?>" method="post"
                                style="display:inline;">
                                <button type="submit" onclick="return confirm('Tem certeza?');">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhuma categoria encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div>
        <?= $pager->links() ?>
    </div>

</body>

</html>