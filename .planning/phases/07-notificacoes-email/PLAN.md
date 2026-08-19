# Plano de Execução — Phase 7: Notificações Transacionais por E-mail (SMTP)

## Objetivo
Implementar um sistema completo de notificações transacionais por e-mail no CodeIgniter 4, enviando e-mails responsivos com design premium no ciclo de vida dos pedidos (**Pedido Realizado**, **Pagamento Aprovado**, **Pedido Enviado** com rastreamento e **Pedido Cancelado**), integrando disparos automáticos nos controllers de checkout, webhook e painel administrativo, com suporte a pré-visualização e teste no painel admin.

---

## Tarefas de Implementação

### 1. Banco de Dados & Migrations
- **Criar Migration para Tabela `pedidos`**:
  - Adicionar coluna `codigo_rastreio` (`VARCHAR(50) NULL`, após `status_pagamento`) para permitir rastreamento de entregas.
- **Atualizar `App\Models\PedidoModel`**:
  - Adicionar `codigo_rastreio` no array `$allowedFields`.

### 2. Configuração de E-mail & Serviços
- **Atualizar `app/Config/Email.php`**:
  - Configurar `$mailType = 'html'`, `$charset = 'UTF-8'`, `$CRLF = "\r\n"`, `$newline = "\r\n"`.
  - Definir remetente padrão (`$fromEmail`, `$fromName`) via variáveis de ambiente (`.env`).
  - Suportar fallback silencioso em ambiente de desenvolvimento/testes para evitar exceções fatais quando o servidor SMTP não estiver configurado.
- **Criar `App\Services\EmailService`**:
  - `enviar(string $para, string $assunto, string $view, array $dados, array $anexos = []): array`
  - `notificarPedidoCriado(int|array $pedido): array`
  - `notificarPagamentoAprovado(int|array $pedido): array`
  - `notificarPedidoEnviado(int|array $pedido, ?string $codigoRastreio = null): array`
  - `notificarPedidoCancelado(int|array $pedido, ?string $motivo = null): array`
  - `testarConexaoSmtp(string $destinatario): array`

### 3. Templates HTML de E-mail (Design System & Rich Aesthetics)
- **Criar Layout Base**:
  - `app/Views/emails/layouts/email_base.php`:
    - Layout centralizado (600px max), tipografia moderna (`system-ui`, `-apple-system`, `sans-serif`), cabeçalho com branding e gradiente sutil, corpo de conteúdo modular e rodapé com dados de contato, links rápidos e termos.
- **Criar Templates Transacionais Específicos**:
  - `app/Views/emails/pedido_criado.php`:
    - Resumo do pedido `#1234`, status inicial, itens comprados (com imagem, atributos/variações, quantidade, preço unitário), subtotal, desconto de cupom, frete, endereço de entrega e instruções de pagamento (Pix Copia e Cola / Cartão).
  - `app/Views/emails/pagamento_aprovado.php`:
    - Confirmação visual de pagamento aprovado com badge verde, informações da transação e próximos passos da expedição.
  - `app/Views/emails/pedido_enviado.php`:
    - Notificação de pedido despachado com código de rastreamento clicável (Correios / Transportadora), modalidade de frete e estimativa de entrega.
  - `app/Views/emails/pedido_cancelado.php`:
    - Notificação de cancelamento com motivo especificado e instruções de atendimento ao cliente / reembolso.

### 4. Integração no Ciclo de Vida dos Pedidos
- **Criação do Pedido**:
  - Em `App\Services\PedidoService::criarPedido()`:
    - Disparar `notificarPedidoCriado()`.
    - Se a forma de pagamento foi Cartão de Crédito e foi aprovado imediatamente, disparar também `notificarPagamentoAprovado()`.
- **Aprovação de Pagamento via Webhook / Gateway**:
  - Em `App\Services\PagamentoService::processarWebhook()`:
    - Ao marcar como pago via Webhook ou aprovação assíncrona, disparar `notificarPagamentoAprovado()`.
- **Atualização de Status pelo Painel Admin**:
  - Em `App\Controllers\Admin\PedidoController`:
    - No método `atualizarStatus()`:
      - Receber campo opcional `codigo_rastreio` ao mudar para `enviado` e salvar no banco.
      - Disparar `notificarPedidoEnviado()` quando o status mudar para `enviado`.
      - Disparar `notificarPagamentoAprovado()` quando o status mudar para `pago`.
      - Disparar `notificarPedidoCancelado()` quando o status mudar para `cancelado`.
    - Adicionar botão de reenvio manual de notificação na tela de detalhes do pedido (`admin/pedidos/detalhe`).

### 5. Pré-Visualização e Teste no Painel Admin
- **Criar `App\Controllers\Admin\EmailPreviewController`**:
  - Rota `GET admin/emails`: Tela de gerenciamento com listagem dos templates, envio de e-mail de teste SMTP e links para preview.
  - Rota `GET admin/emails/preview/(:segment)`: Renderização direta no navegador dos templates (`pedido_criado`, `pagamento_aprovado`, `pedido_enviado`, `pedido_cancelado`) com dados simulados realistas.
  - Rota `POST admin/emails/testar`: Envio de e-mail de teste para verificar a conectividade do servidor SMTP.
- **Atualizar Menu de Navegação do Admin**:
  - Adicionar item "E-mails & Notificações" no menu lateral do painel admin (`app/Views/layouts/admin.php`).

### 6. Testes Automatizados (PHPUnit)
- **Criar `tests/app/EmailServiceTest.php`**:
  - Teste de renderização dos 4 templates de e-mail com verificação de conteúdo e estrutura HTML.
  - Teste unitário dos métodos de notificação (`notificarPedidoCriado`, `notificarPagamentoAprovado`, `notificarPedidoEnviado`, `notificarPedidoCancelado`).
  - Teste do fluxo completo de atualização de status com disparo de e-mail e persistência do código de rastreio.
- **Executar Suite Completa**:
  - Garantir 100% de aprovação no PHPUnit.

---

## Critérios de Aceite
- [ ] Migration executada adicionando `codigo_rastreio` na tabela `pedidos`.
- [ ] `EmailService` criado e configurado com suporte a HTML, charset UTF-8 e envio resiliente.
- [ ] 4 templates HTML responsivos criados e esteticamente polidos (`pedido_criado`, `pagamento_aprovado`, `pedido_enviado`, `pedido_cancelado`).
- [ ] Disparos automáticos integrados no `PedidoService`, `PagamentoService` e `Admin\PedidoController`.
- [ ] Telas de preview e teste de SMTP funcionando no Painel Admin (`/admin/emails`).
- [ ] Suíte de testes PHPUnit cobrindo os templates, serviços e fluxos de e-mail com 100% de sucesso.
