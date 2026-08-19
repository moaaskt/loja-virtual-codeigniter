# Plano de Execução — Phase 6: Gateway de Pagamento (Pix e Cartão de Crédito)

## Objetivo
Integrar um sistema completo e transacional de pagamentos na loja virtual, suportando **Pix** (com geração de QR Code dinâmico, código Copia e Cola e confirmação em tempo real) e **Cartão de Crédito** (com validação completa de dados, detecção de bandeira, cálculo de parcelas e captura), além de um endpoint de **Webhook** para recepção assíncrona de status e atualização automática dos pedidos.

---

## Tarefas de Implementação

### 1. Banco de Dados & Migrations
- **Criar Migration para Tabela `pagamentos`**:
  - `id` (int unsigned, auto_increment, primary key)
  - `pedido_id` (int unsigned, foreign key para `pedidos.id` ON DELETE CASCADE)
  - `metodo` (enum('pix', 'cartao_credito'), not null)
  - `status` (enum('pendente', 'pago', 'falhou', 'cancelado', 'estornado'), default 'pendente')
  - `valor` (decimal(10,2), not null)
  - `transacao_id` (varchar(100), unique, not null)
  - `pix_copiacola` (text, nullable)
  - `pix_qrcode_base64` (text, nullable)
  - `pix_expiracao` (datetime, nullable)
  - `cartao_ultimos_digitos` (varchar(4), nullable)
  - `cartao_bandeira` (varchar(20), nullable)
  - `cartao_parcelas` (int unsigned, default 1)
  - `detalhes_json` (text, nullable)
  - `pago_em` (datetime, nullable)
  - `criado_em` (datetime, nullable)
  - `atualizado_em` (datetime, nullable)
- **Criar Migration para Atualizar `pedidos`**:
  - Adicionar coluna `forma_pagamento` (varchar(30), nullable, after `frete_valor`)
  - Adicionar coluna `status_pagamento` (varchar(30), default 'pendente', after `forma_pagamento`)
  - Ajustar constraint de status ou suporte ao status `pago` no ciclo do pedido.

### 2. Models & Helpers
- **Criar `App\Models\PagamentoModel`**:
  - Configuração de timestamps (`criado_em`, `atualizado_em`).
  - Métodos utilitários:
    - `buscarPorTransacao(string $transacaoId): ?array`
    - `buscarPorPedido(int $pedidoId): ?array`
    - `marcarComoPago(int $pagamentoId, ?string $pagoEm = null): bool`
    - `marcarComoFalho(int $pagamentoId, string $motivo = ''): bool`
- **Atualizar `App\Models\PedidoModel`**:
  - Adicionar `forma_pagamento` e `status_pagamento` no `$allowedFields`.
  - Método `atualizarStatusPagamento(int $pedidoId, string $statusPagamento, ?string $novoStatusPedido = null): bool`.
- **Atualizar `App\Helpers\status_helper.php`**:
  - Suporte visual aos status `pago`, `falhou`, `estornado` com cores e badges adequados (ex: `pago` -> `bg-success`).

### 3. Serviço de Gateway de Pagamento (`PagamentoService`)
- **Criar `App\Services\PagamentoService`**:
  - **Fluxo Pix**:
    - Geração de identificador único de transação (`transacao_id`).
    - Geração de código Pix Copia e Cola (padrão EMV com payload CRC16 válido / chave configurável).
    - Geração de QR Code visual em SVG/Base64 para renderização direta sem bibliotecas externas pesadas.
    - Definição de prazo de expiração (30 minutos).
    - Registro do registro de pagamento em estado `pendente`.
  - **Fluxo Cartão de Crédito**:
    - Validação de número de cartão via Algoritmo de Luhn.
    - Identificação automática de bandeira (Visa, Mastercard, Elo, Amex, Hipercard).
    - Validação de data de validade (mês 01-12, ano presente/futuro) e código de segurança CVV (3 ou 4 dígitos).
    - Validação e cálculo de parcelamento (1x a 12x com cálculo de valor por parcela).
    - Simulação/Processamento de autorização com suporte a cartões de teste (ex: aprovação padrão, simulação de recusa para cartão de teste específico).
    - Registro de pagamento aprovado com status `pago` ou rejeitado com status `falhou` e mensagem explicativa.
  - **Processamento de Webhook**:
    - Recepção e validação do payload de notificação de pagamento.
    - Busca de transação por `transacao_id` ou `pedido_id`.
    - Transição de status atômica com atualização do `PagamentoModel` e `PedidoModel`.

### 4. Integração no Pedido & Checkout
- **Atualizar `App\Services\PedidoService`**:
  - Receber os dados de pagamento (`forma_pagamento`, dados do cartão de crédito ou opção Pix) no método `criarPedido()`.
  - Orquestrar a transação: persistência do pedido, baixa de estoque, aplicação de cupom/frete e geração imediata do pagamento via `PagamentoService`.
  - Em caso de falha de cartão, efetuar rollback seguro e retornar mensagem amigável ao cliente.
  - Para Pix, retornar `pedido_id`, `transacao_id` e dados do QR Code.
- **Atualizar `App\Controllers\PedidoController` & `CarrinhoController`**:
  - Tratar submissão do formulário de checkout com os campos de forma de pagamento.
  - Redirecionamento inteligente:
    - **Pix**: Redireciona para `/pedido/pagamento/{id}` para exibir o QR Code e instruções.
    - **Cartão de Crédito**: Redireciona para `/pedido/sucesso/{id}` ou `/pedido/pagamento/{id}` com confirmação imediata.

### 5. Controladores de Pagamento & Webhook
- **Criar `App\Controllers\PagamentoController`**:
  - `show(int $pedidoId)`: Exibe a página de pagamento do pedido (com QR Code Pix, temporizador regressivo e botão de cópia).
  - `status(int $pedidoId)`: Endpoint JSON (`/api/pedidos/{id}/status-pagamento`) para pooling assíncrono no front-end que detecta aprovação automática do Pix e redireciona o cliente.
- **Criar `App\Controllers\WebhookController`**:
  - `receber()`: Endpoint `POST /api/webhook/pagamento` (isento de CSRF em `Filters.php`) que processa callbacks de pagamento e atualiza status.
  - `simular()`: Endpoint `POST /api/webhook/simular` para testes rápidos em ambiente de desenvolvimento / painel admin.

### 6. Interfaces do Usuário (Front-end)
- **Atualizar `app/Views/shop/carrinho.php` (Offcanvas de Checkout)**:
  - Adicionar seletor interativo de Forma de Pagamento:
    - **Opção Pix**: Destaque com badge "Instantâneo", resumo de pagamento e aviso de geração de QR Code.
    - **Opção Cartão de Crédito**: Inputs modernos com máscara de cartão (`0000 0000 0000 0000`), detecção de bandeira em tempo real, nome impresso, validade (`MM/AA`), CVV (`000`), e seletor de parcelas (1x até 12x com valor exato de cada parcela).
- **Criar `app/Views/shop/pedido_pagamento.php`**:
  - Tela dedicada ao pagamento do pedido:
    - Caixa de destaque do Pix com QR Code nítido, código Copia e Cola e botão com feedback "Código Copiado!".
    - Contador regressivo de 30 minutos para expiração do Pix.
    - Script de polling automático que verifica o status a cada 4 segundos e redireciona para `pedido/sucesso` ao detectar pagamento confirmado.
    - Detalhes do pedido, itens e endereço de entrega.
- **Atualizar `app/Views/shop/pedido_sucesso.php`**:
  - Exibir detalhes da forma de pagamento utilizada, status da transação e botão para visualizar recibo/pedidos.
- **Atualizar `app/Views/cliente/meus_pedidos.php`**:
  - Exibir badge da forma de pagamento (`Pix` ou `Cartão de Crédito`) e botão "Efetuar Pagamento" caso o pedido Pix ainda esteja pendente.
- **Atualizar `app/Views/admin/pedidos/detalhe.php`**:
  - Adicionar card de Informações de Pagamento (Forma de Pagamento, Status do Pagamento, ID da Transação, Bandeira/Parcelas ou Código Pix, e botão para simulação manual de confirmação de Webhook).

### 7. Testes Automatizados (PHPUnit)
- **Criar `tests/app/PagamentoTest.php`**:
  - Teste de geração de Pix (validação de payload, QR Code e expiração).
  - Teste de validação de Cartão de Crédito (Luhn, validade, CVV, bandeiras).
  - Teste de processamento de Cartão (aprovação com sucesso e recusa para cartão inválido).
  - Teste de Webhook (processamento de payload de pagamento aprovado e atualização atômica do status do pedido).
  - Teste integrado de criação de pedido com pagamento.

---

## Critérios de Aceitação (UAT)
1. **Seleção de Pagamento no Checkout**: O cliente pode selecionar entre Pix e Cartão de Crédito no checkout. Se cartão for selecionado, os campos de número, validade, CVV, titular e parcelas são validados.
2. **Geração e Pagamento via Pix**: Ao finalizar com Pix, o pedido é criado com status `pendente`, o cliente é direcionado para a tela de pagamento com QR Code, código Copia e Cola funcional e contador de expiração.
3. **Processamento de Cartão de Crédito**: Ao finalizar com Cartão de Crédito válido, a transação é aprovada, os dados da transação são gravados na tabela `pagamentos` e o pedido é atualizado para `pago`.
4. **Webhook de Pagamento**: Ao enviar uma notificação de pagamento para o endpoint de Webhook, o sistema valida a transação e atualiza o status do pagamento e do pedido para `pago` em tempo real.
5. **Atualização Automática na Interface (Polling)**: Na tela de pagamento Pix, assim que o Webhook aprova o pagamento, a página do cliente detecta a confirmação e atualiza automaticamente para a tela de sucesso.
6. **Visualização no Painel do Cliente e Admin**: O cliente vê a forma de pagamento e status nos "Meus Pedidos", e o administrador visualiza os detalhes completos da transação na tela de Detalhes do Pedido.
7. **Bateria de Testes Automatizados**: Todos os testes em `tests/app/PagamentoTest.php` e os testes legados passam com 100% de sucesso via PHPUnit.
