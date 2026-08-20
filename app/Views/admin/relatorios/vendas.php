<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-cash-stack text-success me-2"></i>Relatório de Vendas</h1>
        <p class="text-muted small mb-0">Listagem analítica e detalhada de todos os pedidos realizados no período.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/relatorios/exportar/vendas?' . http_build_query(array_merge($filtros, [
            'periodo'     => $periodoInfo['periodo'],
            'data_inicio' => $periodoInfo['data_inicio_formatada'],
            'data_fim'    => $periodoInfo['data_fim_formatada'],
        ]))) ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar Vendas (CSV)
        </a>
    </div>
</div>

<!-- ===== FILTROS AVANÇADOS ===== -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= site_url('admin/relatorios/vendas') ?>" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted mb-1">Período Pré-definido</label>
                <select name="periodo" class="form-select form-select-sm" onchange="if(this.value !== 'custom') this.form.submit();">
                    <option value="hoje" <?= $periodoInfo['periodo'] === 'hoje' ? 'selected' : '' ?>>Hoje</option>
                    <option value="7d" <?= $periodoInfo['periodo'] === '7d' ? 'selected' : '' ?>>Últimos 7 Dias</option>
                    <option value="30d" <?= $periodoInfo['periodo'] === '30d' ? 'selected' : '' ?>>Últimos 30 Dias</option>
                    <option value="mes_atual" <?= $periodoInfo['periodo'] === 'mes_atual' ? 'selected' : '' ?>>Mês Atual</option>
                    <option value="ano_atual" <?= $periodoInfo['periodo'] === 'ano_atual' ? 'selected' : '' ?>>Ano Atual</option>
                    <option value="custom" <?= $periodoInfo['periodo'] === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small text-muted mb-1">Data Início</label>
                <input type="date" class="form-control form-control-sm" name="data_inicio" value="<?= esc($periodoInfo['data_inicio_formatada']) ?>">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small text-muted mb-1">Data Fim</label>
                <input type="date" class="form-control form-control-sm" name="data_fim" value="<?= esc($periodoInfo['data_fim_formatada']) ?>">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small text-muted mb-1">Status do Pedido</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos os status</option>
                    <option value="pendente" <?= ($filtros['status'] ?? '') === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="pago" <?= ($filtros['status'] ?? '') === 'pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="enviado" <?= ($filtros['status'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                    <option value="entregue" <?= ($filtros['status'] ?? '') === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                    <option value="cancelado" <?= ($filtros['status'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small text-muted mb-1">Forma Pagamento</label>
                <select name="forma_pagamento" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="pix" <?= ($filtros['forma_pagamento'] ?? '') === 'pix' ? 'selected' : '' ?>>Pix</option>
                    <option value="cartao_credito" <?= ($filtros['forma_pagamento'] ?? '') === 'cartao_credito' ? 'selected' : '' ?>>Cartão de Crédito</option>
                </select>
            </div>

            <div class="col-12 col-md-1 d-flex align-items-end mt-2 mt-md-4">
                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== RESUMO DAS VENDAS FILTRADAS ===== -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card p-3 border-0 shadow-sm">
            <span class="text-muted small">Total de Pedidos Listados</span>
            <span class="fs-4 fw-bold text-dark"><?= count($vendas) ?></span>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-3 border-0 shadow-sm">
            <span class="text-muted small">Faturamento Filtrado</span>
            <?php
            $faturamentoFiltrado = 0;
            foreach ($vendas as $v) {
                if (in_array($v['status'], ['pago', 'enviado', 'entregue'])) {
                    $faturamentoFiltrado += (float) $v['valor_total'];
                }
            }
            ?>
            <span class="fs-4 fw-bold text-success">R$ <?= number_format($faturamentoFiltrado, 2, ',', '.') ?></span>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-3 border-0 shadow-sm">
            <span class="text-muted small">Total em Descontos</span>
            <?php
            $descontoFiltrado = 0;
            foreach ($vendas as $v) {
                $descontoFiltrado += (float) ($v['desconto_valor'] ?? 0);
            }
            ?>
            <span class="fs-4 fw-bold text-danger">R$ <?= number_format($descontoFiltrado, 2, ',', '.') ?></span>
        </div>
    </div>
</div>

<!-- ===== TABELA DETALHADA ===== -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th>Pedido</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Forma Pagamento</th>
                    <th>Cupom</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Frete</th>
                    <th class="text-end">Desconto</th>
                    <th class="text-end">Valor Total</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendas)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Nenhuma venda encontrada com os filtros selecionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vendas as $v): ?>
                        <tr>
                            <td class="fw-semibold"><a href="<?= site_url('admin/pedidos/detalhe/' . $v['id']) ?>" class="text-decoration-none">#<?= $v['id'] ?></a></td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($v['criado_em'])) ?></td>
                            <td>
                                <div class="fw-medium small"><?= esc($v['cliente_nome'] ?? 'Cliente') ?></div>
                                <small class="text-muted" style="font-size:.75rem;"><?= esc($v['cliente_email'] ?? '') ?></small>
                            </td>
                            <td>
                                <?php if ($v['forma_pagamento'] === 'pix'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-qr-code me-1"></i>Pix</span>
                                <?php elseif ($v['forma_pagamento'] === 'cartao_credito' || $v['forma_pagamento'] === 'cartao'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-credit-card me-1"></i>Cartão</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border"><?= esc(ucfirst($v['forma_pagamento'] ?? 'N/A')) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($v['cupom_codigo'])): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-ticket-perforated me-1"></i><?= esc($v['cupom_codigo']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= helper('status') ? getBadgeStatusPedido($v['status']) : '<span class="badge bg-secondary">' . esc(ucfirst($v['status'])) . '</span>' ?>
                            </td>
                            <td class="text-end small text-muted">R$ <?= number_format((float) ($v['frete_valor'] ?? 0), 2, ',', '.') ?></td>
                            <td class="text-end small <?= (float) ($v['desconto_valor'] ?? 0) > 0 ? 'text-danger fw-semibold' : 'text-muted' ?>">
                                <?= (float) ($v['desconto_valor'] ?? 0) > 0 ? '- R$ ' . number_format((float) $v['desconto_valor'], 2, ',', '.') : 'R$ 0,00' ?>
                            </td>
                            <td class="text-end fw-bold text-dark">R$ <?= number_format((float) $v['valor_total'], 2, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/pedidos/detalhe/' . $v['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
