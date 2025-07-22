<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
</head>
<body>
    <h1><?= esc($title) ?></h1>

    <?php if (!empty(session()->getFlashdata('errors'))): ?>
        <div style="color: red;">
            <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('categorias/' . $categoria['id']) ?>" method="post">
    <?= csrf_field() ?> 

        <label for="nome">Nome da Categoria</label>
        <input type="text" name="nome" id="nome" value="<?= old('nome', $categoria['nome']) ?>">

        <br><br>

        <label for="descricao">Descrição</label>
        <textarea name="descricao" id="descricao"><?= old('descricao', $categoria['descricao']) ?></textarea>

        <br><br>

        <button type="submit">Atualizar</button>
        <a href="<?= site_url('categorias') ?>">Cancelar</a>

    </form>


</body>
</html>