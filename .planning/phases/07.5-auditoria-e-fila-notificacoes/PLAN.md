# Plano de Execução — Phase 7.5: Auditoria do Sistema & Fila de Notificações

## Objetivo
Implementar uma infraestrutura completa e desacoplada para:
1. **Trilha de Auditoria (Audit Trail)**: Registro detalhado de ações críticas no sistema (usuário, ação, entidade, registro afetado, dados anteriores, dados novos, IP e User-Agent).
2. **Fila e Histórico de Notificações**: Persistência e monitoramento resiliente de disparos de mensagens (e-mail e futuros canais como WhatsApp), permitindo visualização de logs, status de entrega e reprocessamento/reenvio de falhas diretamente pelo painel administrativo.

---

## Detalhamento das Tarefas de Implementação

### 1. Banco de Dados & Migrations

#### A. Migration e Model `audit_logs`
- **Migration**: `app/Database/Migrations/xxxx_xx_xx_xxxxxx_CreateAuditLogsTable.php`
  - `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
  - `usuario_id`: `INT UNSIGNED NULL` (chave estrangeira opcional para `usuarios.id` com `ON DELETE SET NULL`)
  - `acao`: `VARCHAR(50)` (ex: `create`, `update`, `delete`, `status_change`, `login`, `checkout`)
  - `entidade`: `VARCHAR(50)` (ex: `pedidos`, `produtos`, `usuarios`, `cupons`)
  - `registro_id`: `INT UNSIGNED NULL`
  - `dados_anteriores`: `LONGTEXT / JSON NULL`
  - `dados_novos`: `LONGTEXT / JSON NULL`
  - `ip`: `VARCHAR(45) NULL`
  - `user_agent`: `VARCHAR(255) NULL`
  - `created_at`: `DATETIME`
- **Model**: `App\Models\AuditLogModel`
  - Tabela: `audit_logs`
  - `$allowedFields`: `['usuario_id', 'acao', 'entidade', 'registro_id', 'dados_anteriores', 'dados_novos', 'ip', 'user_agent', 'created_at']`
  - Suporte a filtros de busca por usuário, ação, entidade e período.

#### B. Migration e Model `notification_logs`
- **Migration**: `app/Database/Migrations/xxxx_xx_xx_xxxxxx_CreateNotificationLogsTable.php`
  - `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
  - `canal`: `ENUM('email', 'whatsapp') DEFAULT 'email'`
  - `destinatario`: `VARCHAR(255)`
  - `evento`: `VARCHAR(100)` (ex: `pedido_criado`, `pagamento_aprovado`, `pedido_enviado`, `pedido_cancelado`, `teste_smtp`)
  - `payload`: `LONGTEXT / JSON NULL` (dados estruturados e parâmetros enviados)
  - `status`: `ENUM('pendente', 'enviado', 'falhou') DEFAULT 'pendente'`
  - `tentativas`: `INT DEFAULT 1`
  - `mensagem_erro`: `TEXT NULL`
  - `enviado_em`: `DATETIME NULL`
  - `created_at`: `DATETIME`
  - `updated_at`: `DATETIME`
- **Model**: `App\Models\NotificationLogModel`
  - Tabela: `notification_logs`
  - `$allowedFields`: `['canal', 'destinatario', 'evento', 'payload', 'status', 'tentativas', 'mensagem_erro', 'enviado_em', 'created_at', 'updated_at']`
  - Métodos auxiliares para buscar logs com paginação, filtros por canal/status/evento e contadores de falhas.

---

### 2. Serviços de Infraestrutura

#### A. `App\Services\AuditService`
- Serviço estático/desacoplado para facilitar chamadas em qualquer ponto da aplicação.
- Assinatura:
  ```php
  public static function log(
      string $acao,
      string $entidade,
      ?int $registroId = null,
      array|object|null $dadosNovos = null,
      array|object|null $dadosAnteriores = null,
      ?int $usuarioId = null
  ): bool
  ```
- Captura automática de `IP`, `User-Agent` e do `usuario_id` logado na sessão caso não seja passado explicitamente.
- Serialização segura em JSON dos dados passados.

#### B. Refatoração do `App\Services\EmailService`
- Injeção/Uso do `NotificationLogModel` para cada disparo de e-mail.
- Ao chamar `enviar()`:
  1. Cria registro inicial ou atualiza log com status `pendente`/`enviado`/`falhou`.
  2. Em caso de sucesso: status atualizado para `enviado`, `enviado_em = date('Y-m-d H:i:s')`.
  3. Em caso de falha: status marcado como `falhou`, captura do erro em `mensagem_erro` e incremento do contador `tentativas`.
- Método de reprocessamento:
  ```php
  public function reprocessarNotificacao(int $logId): array
  ```
  - Lê o registro de `notification_logs`, recupera o payload/template e tenta reenviar, atualizando o status de acordo com o resultado.

---

### 3. Painel Admin / Interface

#### A. Trilha de Auditoria (`/admin/auditoria`)
- **Controller**: `App\Controllers\Admin\AuditoriaController`
  - `index()`: Listagem paginada dos logs de auditoria com:
    - Filtro por Ação (`create`, `update`, `delete`, etc.).
    - Filtro por Entidade (`pedidos`, `produtos`, etc.).
    - Busca textual por palavra-chave (nos dados alterados ou IP).
    - Modal/Drawer de detalhe visual comparativo (diff formatado em JSON ou visual) de `dados_anteriores` vs `dados_novos`.
- **View**: `app/Views/admin/auditoria/index.php` com design system consistente e badges informativos.

#### B. Monitor da Fila de Notificações (`/admin/notificacoes/fila`)
- **Controller**: `App\Controllers\Admin\NotificacaoFilaController`
  - `index()`: Tabela de monitoramento com filtros por status (`pendente`, `enviado`, `falhou`), canal e busca por destinatário/evento.
  - `reprocessar(int $id)`: Endpoint POST/AJAX para tentar reenviar uma notificação com falha.
  - `limparAntigos()`: Opcional para manutenção e purga de logs antigos enviados.
- **View**: `app/Views/admin/notificacoes/fila.php` com contadores de resumo (Total, Enviados, Falhas), badges de status e botão interativo "Reenviar / Reprocessar".

#### C. Menu Lateral do Admin
- **View**: `app/Views/layouts/admin.php`
  - Adicionar itens no menu:
    - **Auditoria** (`/admin/auditoria`) com ícone representativo (ex: `bi-shield-check` ou `bi-clock-history`).
    - **Fila de Notificações** (`/admin/notificacoes/fila`) com contador/badge de falhas e ícone representativo (ex: `bi-send-check` ou `bi-envelope-paper`).

---

### 4. Testes Automatizados (PHPUnit)

#### A. `tests/app/AuditServiceTest.php`
- Testar registro automático de auditoria com `AuditService::log()`.
- Validar persistência de IP, User-Agent e serialização de arrays/JSON no banco.
- Testar auditoria anônima vs autenticada.

#### B. `tests/app/NotificationLogTest.php`
- Testar criação de log no envio de e-mails via `EmailService`.
- Testar atualização de status para `enviado` e `falhou`.
- Testar o reprocessamento de notificações falhas (`reprocessarNotificacao`).

---

## Critérios de Aceite
- [ ] Migrations e Models criados para `audit_logs` e `notification_logs`.
- [ ] `AuditService` implementado e pronto para uso global.
- [ ] `EmailService` refatorado para registrar automaticamente no `notification_logs` e permitir reprocessamento.
- [ ] Interface de Trilha de Auditoria criada em `/admin/auditoria` com filtros funcionais.
- [ ] Interface de Fila de Notificações criada em `/admin/notificacoes/fila` com status e reprocessamento funcional.
- [ ] Menu do Admin atualizado com links diretos para as novas telas.
- [ ] Suíte de testes `AuditServiceTest.php` e `NotificationLogTest.php` cobrindo 100% dos fluxos com sucesso.
