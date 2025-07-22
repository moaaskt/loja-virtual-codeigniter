<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-4 shadow-sm">
                <div class="card-header">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="card-body">
                    <?= validation_list_errors() ?>
                    <?= form_open('registrar/salvar') ?>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" value="<?= old('nome') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha_hash" class="form-label">Senha</label>
                            <input type="password" name="senha_hash" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmar Senha</label>
                            <input type="password" name="password_confirm" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Criar Conta</button>
                    <?= form_close() ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>Já tem uma conta? <a href="<?= site_url('login') ?>">Faça login</a></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>