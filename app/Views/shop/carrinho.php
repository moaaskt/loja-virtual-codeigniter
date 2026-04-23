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
                            <img src="<?= strpos($item['imagem'], 'http') === 0 ? esc($item['imagem']) : base_url('uploads/produtos/' . esc($item['imagem'])) ?>"
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

        <div class="d-flex justify-content-start mt-2 mb-4">
            <a href="<?= site_url('/') ?>" class="btn btn-secondary">Continuar Comprando</a>
        </div>

        <!-- Endereço de Entrega -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Endereço de Entrega</h5>
            </div>
            <div class="card-body">
                <?= form_open('checkout/finalizar', ['id' => 'form-checkout']) ?>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="cep" class="form-label">CEP <span class="text-danger">*</span></label>
                        <input type="text" id="cep" name="cep" class="form-control" placeholder="00000-000"
                            maxlength="9" required>
                        <div id="cep-feedback" class="form-text text-danger d-none">CEP não encontrado.</div>
                    </div>

                    <div class="col-md-7">
                        <label for="logradouro" class="form-label">Logradouro <span class="text-danger">*</span></label>
                        <input type="text" id="logradouro" name="logradouro" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label for="numero" class="form-label">Número <span class="text-danger">*</span></label>
                        <input type="text" id="numero" name="numero" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto, bloco...">
                    </div>

                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro <span class="text-danger">*</span></label>
                        <input type="text" id="bairro" name="bairro" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label for="cidade" class="form-label">Cidade <span class="text-danger">*</span></label>
                        <input type="text" id="cidade" name="cidade" class="form-control" required>
                    </div>

                    <div class="col-md-1">
                        <label for="uf" class="form-label">UF <span class="text-danger">*</span></label>
                        <input type="text" id="uf" name="uf" class="form-control" maxlength="2" required>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg">Finalizar Compra — R$ <?= esc(number_format($total, 2, ',', '.')) ?></button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cepInput    = document.getElementById('cep');
    const feedback    = document.getElementById('cep-feedback');

    if (!cepInput) return;

    cepInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
    });

    cepInput.addEventListener('blur', async function () {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        feedback.classList.add('d-none');

        try {
            const res  = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await res.json();

            if (data.erro) {
                feedback.classList.remove('d-none');
                return;
            }

            document.getElementById('logradouro').value = data.logradouro || '';
            document.getElementById('bairro').value     = data.bairro     || '';
            document.getElementById('cidade').value     = data.localidade  || '';
            document.getElementById('uf').value         = data.uf          || '';
            document.getElementById('numero').focus();
        } catch (e) {
            feedback.classList.remove('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>
