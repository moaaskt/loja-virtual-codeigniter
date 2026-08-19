<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $isEdit = !empty($cupom);
    $actionUrl = $isEdit ? site_url('admin/cupons/update/' . $cupom['id']) : site_url('admin/cupons/create');
?>

<div class="page-header">
    <div>
        <h1>
            <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-plus-circle-fill' ?> text-primary me-2"></i>
            <?= esc($title) ?>
        </h1>
        <p class="text-muted small mb-0"><?= $isEdit ? 'Atualize as regras deste cupom' : 'Cadastre um novo código de desconto' ?></p>
    </div>
    <a href="<?= site_url('admin/cupons') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-cupons">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card" style="max-width:760px;">
    <div class="card-body p-4">
        <?= validation_list_errors() ?>

        <?= form_open($actionUrl) ?>

        <div class="row g-3">
            <!-- Código -->
            <div class="col-md-6">
                <label for="codigo" class="form-label fw-semibold">Código do Cupom <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                    <input type="text" name="codigo" id="codigo" class="form-control text-uppercase font-monospace"
                        value="<?= old('codigo', $cupom['codigo'] ?? '') ?>"
                        placeholder="Ex: PROMO10" required maxlength="50">
                </div>
                <small class="text-muted">Exibido para o cliente no carrinho.</small>
            </div>

            <!-- Tipo -->
            <div class="col-md-6">
                <label for="tipo" class="form-label fw-semibold">Tipo de Desconto <span class="text-danger">*</span></label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="porcentagem" <?= old('tipo', $cupom['tipo'] ?? '') === 'porcentagem' ? 'selected' : '' ?>>
                        Porcentagem (% de desconto)
                    </option>
                    <option value="fixo" <?= old('tipo', $cupom['tipo'] ?? '') === 'fixo' ? 'selected' : '' ?>>
                        Valor Fixo (R$ em dinheiro)
                    </option>
                </select>
            </div>

            <!-- Valor do Desconto -->
            <div class="col-md-6">
                <label for="valor" class="form-label fw-semibold">Valor do Desconto <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" id="prefixo-valor">%</span>
                    <input type="number" step="0.01" min="0.01" name="valor" id="valor" class="form-control"
                        value="<?= old('valor', $cupom['valor'] ?? '') ?>"
                        placeholder="Ex: 10.00" required>
                </div>
                <small class="text-muted" id="ajuda-valor">Para 10%, digite 10. Para R$ 20, digite 20.00.</small>
            </div>

            <!-- Valor Mínimo de Pedido -->
            <div class="col-md-6">
                <label for="valor_minimo_pedido" class="form-label fw-semibold">Pedido Mínimo (R$)</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" min="0" name="valor_minimo_pedido" id="valor_minimo_pedido" class="form-control"
                        value="<?= old('valor_minimo_pedido', $cupom['valor_minimo_pedido'] ?? '0.00') ?>"
                        placeholder="0.00">
                </div>
                <small class="text-muted">Deixe 0.00 para permitir em qualquer valor de compra.</small>
            </div>

            <!-- Limite de Usos -->
            <div class="col-md-6">
                <label for="limite_uso" class="form-label fw-semibold">Limite de Utilizações</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-people"></i></span>
                    <input type="number" min="1" name="limite_uso" id="limite_uso" class="form-control"
                        value="<?= old('limite_uso', $cupom['limite_uso'] ?? '') ?>"
                        placeholder="Ilimitado">
                </div>
                <small class="text-muted">Deixe em branco para permitir utilizações ilimitadas.</small>
            </div>

            <!-- Data de Validade -->
            <div class="col-md-6">
                <label for="data_validade" class="form-label fw-semibold">Data de Validade</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                    <input type="date" name="data_validade" id="data_validade" class="form-control"
                        value="<?= old('data_validade', $cupom['data_validade'] ?? '') ?>">
                </div>
                <small class="text-muted">Deixe em branco para cupom sem data de expiração.</small>
            </div>

            <!-- Status Ativo -->
            <div class="col-12 mt-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="ativo" name="ativo" value="1"
                        <?= old('ativo', $cupom['ativo'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="ativo">Cupom Ativo para uso imediato</label>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 pt-4 mt-4 border-top">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" id="btn-salvar-cupom">
                <i class="bi bi-save me-2"></i><?= $isEdit ? 'Atualizar Cupom' : 'Criar Cupom' ?>
            </button>
            <a href="<?= site_url('admin/cupons') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                Cancelar
            </a>
        </div>

        <?= form_close() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipo = document.getElementById('tipo');
    const prefixo    = document.getElementById('prefixo-valor');
    const ajuda      = document.getElementById('ajuda-valor');
    const inputCod   = document.getElementById('codigo');

    if (inputCod) {
        inputCod.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/\s+/g, '');
        });
    }

    function atualizarTipo() {
        if (!selectTipo || !prefixo) return;
        if (selectTipo.value === 'porcentagem') {
            prefixo.textContent = '%';
            if (ajuda) ajuda.textContent = 'Informe a porcentagem de desconto (Ex.: 15 para 15% OFF).';
        } else {
            prefixo.textContent = 'R$';
            if (ajuda) ajuda.textContent = 'Informe o valor em reais a ser descontado (Ex.: 25.00).';
        }
    }

    if (selectTipo) {
        selectTipo.addEventListener('change', atualizarTipo);
        atualizarTipo();
    }
});
</script>

<?= $this->endSection() ?>
