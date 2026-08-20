<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1 text-gray-800 fw-bold">
            <i class="bi bi-send-check-fill text-primary me-2"></i>Fila & Histórico de Notificações
        </h1>
        <p class="text-muted mb-0 small">Acompanhamento em tempo real de mensagens transacionais (E-mail / WhatsApp) e reprocessamento de falhas.</p>
    </div>
</div>

<?php if (session()->getFlashdata('sucesso')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('sucesso') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('erro')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('erro') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total de Disparos</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($estatisticas['total'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-collection-fill fs-2 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Enviados com Sucesso</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($estatisticas['enviados'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check2-all fs-2 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-warning border-4 h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pendentes na Fila</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($estatisticas['pendentes'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-hourglass-split fs-2 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Falhas de Envio</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($estatisticas['falhas'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-octagon-fill fs-2 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Busca -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/notificacoes/fila') ?>" class="row g-3">
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Canal</label>
                <select name="canal" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="email" <?= ($filtros['canal'] ?? '') === 'email' ? 'selected' : '' ?>>E-mail</option>
                    <option value="whatsapp" <?= ($filtros['canal'] ?? '') === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="enviado" <?= ($filtros['status'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                    <option value="pendente" <?= ($filtros['status'] ?? '') === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="falhou" <?= ($filtros['status'] ?? '') === 'falhou' ? 'selected' : '' ?>>Falhou</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Evento</label>
                <input type="text" name="evento" class="form-control form-control-sm" placeholder="Ex: pedido_criado, teste_smtp" value="<?= esc($filtros['evento'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Destinatário / Erro</label>
                <input type="text" name="busca" class="form-control form-control-sm" placeholder="Buscar e-mail ou motivo de erro..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i>Filtrar
                </button>
                <a href="<?= site_url('admin/notificacoes/fila') ?>" class="btn btn-sm btn-outline-secondary" title="Limpar Filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabela da Fila -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Canal</th>
                        <th>Destinatário</th>
                        <th>Evento</th>
                        <th>Status</th>
                        <th>Tentativas</th>
                        <th>Data Criação / Envio</th>
                        <th style="width: 120px;" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-envelope-check fs-1 d-block mb-2 text-secondary"></i>
                                Nenhuma notificação encontrada na fila ou histórico.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="fw-semibold text-muted">#<?= $log['id'] ?></td>
                                <td>
                                    <?php if ($log['canal'] === 'email'): ?>
                                        <span class="badge bg-primary"><i class="bi bi-envelope-fill me-1"></i>E-mail</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="bi bi-whatsapp me-1"></i>WhatsApp</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($log['destinatario']) ?></div>
                                    <?php if (!empty($log['mensagem_erro'])): ?>
                                        <div class="text-danger small text-truncate" style="max-width: 250px;" title="<?= esc($log['mensagem_erro']) ?>">
                                            <i class="bi bi-exclamation-circle me-1"></i><?= esc($log['mensagem_erro']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= esc($log['evento']) ?></span>
                                </td>
                                <td>
                                    <?php if ($log['status'] === 'enviado'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Enviado</span>
                                    <?php elseif ($log['status'] === 'pendente'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pendente</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Falhou</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $log['tentativas'] ?>x</span>
                                </td>
                                <td class="small text-nowrap">
                                    <div><i class="bi bi-plus-circle me-1 text-muted"></i><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></div>
                                    <?php if (!empty($log['enviado_em'])): ?>
                                        <div class="text-success"><i class="bi bi-check2 me-1"></i><?= date('d/m/Y H:i', strtotime($log['enviado_em'])) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form method="post" action="<?= site_url('admin/notificacoes/reprocessar/' . $log['id']) ?>" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Reprocessar Disparo Agora">
                                            <i class="bi bi-arrow-repeat me-1"></i>Reenviar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer bg-white border-0 py-3">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
