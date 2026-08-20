<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1 text-gray-800 fw-bold">
            <i class="bi bi-shield-lock-fill text-primary me-2"></i>Trilha de Auditoria
        </h1>
        <p class="text-muted mb-0 small">Histórico completo de alterações e eventos críticos realizados no sistema.</p>
    </div>
</div>

<!-- Filtros de Busca -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/auditoria') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Usuário</label>
                <select name="usuario_id" class="form-select form-select-sm">
                    <option value="">Todos os usuários</option>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($filtros['usuario_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= esc($u['nome']) ?> (<?= esc($u['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Ação</label>
                <select name="acao" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="create" <?= ($filtros['acao'] ?? '') === 'create' ? 'selected' : '' ?>>Create (Criação)</option>
                    <option value="update" <?= ($filtros['acao'] ?? '') === 'update' ? 'selected' : '' ?>>Update (Atualização)</option>
                    <option value="delete" <?= ($filtros['acao'] ?? '') === 'delete' ? 'selected' : '' ?>>Delete (Exclusão)</option>
                    <option value="status_change" <?= ($filtros['acao'] ?? '') === 'status_change' ? 'selected' : '' ?>>Mudança de Status</option>
                    <option value="login" <?= ($filtros['acao'] ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Entidade</label>
                <input type="text" name="entidade" class="form-control form-control-sm" placeholder="Ex: pedidos, produtos" value="<?= esc($filtros['entidade'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Palavra-chave / IP</label>
                <input type="text" name="busca" class="form-control form-control-sm" placeholder="Buscar no diff, IP ou nome..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i>Filtrar
                </button>
                <a href="<?= site_url('admin/auditoria') ?>" class="btn btn-sm btn-outline-secondary" title="Limpar Filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Logs -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 160px;">Data / Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Entidade / ID</th>
                        <th>IP / Agente</th>
                        <th style="width: 100px;" class="text-center">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                                Nenhum registro de auditoria encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="fw-semibold text-muted">#<?= $log['id'] ?></td>
                                <td class="small text-nowrap">
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>
                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if (!empty($log['usuario_nome'])): ?>
                                        <div class="fw-bold"><?= esc($log['usuario_nome']) ?></div>
                                        <small class="text-muted"><?= esc($log['usuario_email']) ?></small>
                                    <?php elseif (!empty($log['usuario_id'])): ?>
                                        <span class="badge bg-secondary">ID #<?= $log['usuario_id'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Sistema / Anônimo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $acaoClass = match($log['acao']) {
                                        'create'        => 'bg-success',
                                        'update'        => 'bg-info text-dark',
                                        'delete'        => 'bg-danger',
                                        'status_change' => 'bg-warning text-dark',
                                        'login'         => 'bg-primary',
                                        default         => 'bg-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $acaoClass ?> text-uppercase" style="font-size: 0.725rem;">
                                        <?= esc($log['acao']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary"><?= esc($log['entidade']) ?></span>
                                    <?php if ($log['registro_id']): ?>
                                        <span class="text-muted small">#<?= $log['registro_id'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <div><?= esc($log['ip'] ?? 'N/A') ?></div>
                                    <div class="text-truncate" style="max-width: 150px;" title="<?= esc($log['user_agent'] ?? '') ?>">
                                        <?= esc($log['user_agent'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary btn-ver-detalhes"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDiff"
                                            data-id="<?= $log['id'] ?>"
                                            data-acao="<?= esc($log['acao']) ?>"
                                            data-entidade="<?= esc($log['entidade']) ?> #<?= $log['registro_id'] ?>"
                                            data-anteriores="<?= esc($log['dados_anteriores'] ?? '{}') ?>"
                                            data-novos="<?= esc($log['dados_novos'] ?? '{}') ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
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

<!-- Modal Visualizador de Diff / Dados -->
<div class="modal fade" id="modalDiff" tabindex="-1" aria-labelledby="modalDiffLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiffLabel">
                    <i class="bi bi-file-earmark-diff text-primary me-2"></i>Detalhe da Alteração <span id="modalDiffSubtitle" class="badge bg-light text-dark border ms-2"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-danger">
                            <i class="bi bi-dash-circle me-1"></i>Dados Anteriores
                        </label>
                        <pre class="bg-light p-3 rounded border text-muted small" id="preDadosAnteriores" style="max-height: 350px; overflow-y: auto;">-</pre>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-success">
                            <i class="bi bi-plus-circle me-1"></i>Dados Novos
                        </label>
                        <pre class="bg-light p-3 rounded border text-dark small" id="preDadosNovos" style="max-height: 350px; overflow-y: auto;">-</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalDiff = document.getElementById('modalDiff');
    modalDiff.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const acao = button.getAttribute('data-acao');
        const entidade = button.getAttribute('data-entidade');
        const anteriores = button.getAttribute('data-anteriores');
        const novos = button.getAttribute('data-novos');

        document.getElementById('modalDiffSubtitle').textContent = `${acao.toUpperCase()} — ${entidade}`;
        
        try {
            document.getElementById('preDadosAnteriores').textContent = anteriores && anteriores !== 'null' ? JSON.stringify(JSON.parse(anteriores), null, 2) : 'Nenhum dado anterior.';
        } catch (e) {
            document.getElementById('preDadosAnteriores').textContent = anteriores || '-';
        }

        try {
            document.getElementById('preDadosNovos').textContent = novos && novos !== 'null' ? JSON.stringify(JSON.parse(novos), null, 2) : 'Nenhum dado novo.';
        } catch (e) {
            document.getElementById('preDadosNovos').textContent = novos || '-';
        }
    });
});
</script>
<?= $this->endSection() ?>
