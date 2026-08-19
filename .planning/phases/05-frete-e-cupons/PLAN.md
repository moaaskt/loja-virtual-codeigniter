# Plano de Execução — Phase 5: Cálculo de Frete e Cupons de Desconto

## Objetivo
Implementar a simulação e cálculo de frete por CEP (página de produto, carrinho e checkout) e o sistema completo de Cupons de Desconto (banco de dados, CRUD administrativo, validação de regras e aplicação no carrinho/checkout com recálculo de pedidos).

---

## Tarefas de Implementação

### 1. Banco de Dados & Migrations
- **Criar Migration para Tabela `cupons`**:
  - `id` (int unsigned, auto_increment, primary key)
  - `codigo` (varchar(50), unique, not null)
  - `tipo` (enum('fixo', 'porcentagem'), default 'porcentagem')
  - `valor` (decimal(10,2), not null)
  - `valor_minimo_pedido` (decimal(10,2), default 0.00)
  - `limite_uso` (int unsigned, default null)
  - `vezes_usado` (int unsigned, default 0)
  - `data_validade` (date, default null)
  - `ativo` (tinyint(1), default 1)
  - `created_at` (datetime)
  - `updated_at` (datetime)
- **Criar Migration para Campos Extras em `pedidos`**:
  - `cupom_codigo` (varchar(50), default null)
  - `desconto_valor` (decimal(10,2), default 0.00)
  - `frete_modalidade` (varchar(50), default null)
  - `frete_valor` (decimal(10,2), default 0.00)

### 2. Serviço de Frete & Endpoints
- **Criar `App\Services\FreteService`**:
  - Regras de cálculo baseadas em faixas de CEP brasileiras (Capitais, Interior, Sudeste, Sul, Nordeste, Centro-Oeste, Norte).
  - Modalidades: PAC / Econômico (5 a 10 dias), SEDEX / Expresso (1 a 3 dias) e Frete Grátis (para produtos com `frete_gratis = 1` ou pedidos com subtotal >= R$ 199,00).
- **Criar `App\Controllers\FreteController`**:
  - Método `calcular()`: endpoint JSON para simulação de frete a partir de CEP e subtotal/produto.
- **Atualizar `CarrinhoController` & `CarrinhoService`**:
  - Métodos `selecionarFrete()` e `limparFrete()` armazenando a modalidade e valor na sessão `session()->set('frete', [...])`.

### 3. Model & CRUD de Cupons no Painel Administrativo
- **Criar `App\Models\CupomModel`**:
  - Regras de validação de formulário.
  - Método `validarCupom(string $codigo, float $subtotal): array` (verifica existência, status ativo, data de validade, limite de usos e valor mínimo).
  - Método `incrementarUso(int $cupomId): void`.
- **Criar `App\Controllers\Admin\CuponsController`**:
  - `index()`: listagem com badge de status, tipo, valor e total de utilizações.
  - `new()` & `create()`: criação com validações.
  - `edit()` & `update()`: edição.
  - `delete()`: exclusão.
  - `toggle()`: ativação/desativação rápida.
- **Criar Views do Admin**:
  - `app/Views/admin/cupons/index.php`
  - `app/Views/admin/cupons/form.php`
- **Atualizar Menu do Admin**:
  - Adicionar link de "Cupons de Desconto" em `app/Views/layouts/admin.php`.

### 4. Aplicação de Cupons no Carrinho e Checkout (Cliente)
- **Atualizar `CarrinhoController` & `CarrinhoService`**:
  - Método `aplicarCupom()`: valida o código informado, calcula o desconto e salva na sessão `session()->set('cupom', [...])`.
  - Método `removerCupom()`: remove o cupom da sessão.
  - Método `calcularTotais()`: consolida Subtotal, Frete, Desconto e Total Final.
- **Atualizar Views do Cliente**:
  - `app/Views/shop/produto_detalhe.php`: Adicionar box de simulação de frete com input de CEP e tabela de opções (PAC, SEDEX, Frete Grátis).
  - `app/Views/shop/carrinho.php`:
    - Adicionar box de cálculo/seleção de frete.
    - Adicionar box de inserção de Cupom de Desconto com feedback visual (sucesso/erro) e botão de remoção.
    - Atualizar o resumo do pedido (Subtotal + Frete - Desconto = Total).
    - Atualizar o formulário de checkout offcanvas com o frete e cupom selecionados.

### 5. Finalização de Pedidos com Frete e Desconto
- **Atualizar `App\Services\PedidoService`**:
  - Persistir `cupom_codigo`, `desconto_valor`, `frete_modalidade` e `frete_valor` no pedido.
  - Calcular o `valor_total` final considerando o frete e o desconto.
  - Incrementar o contador de uso do cupom utilizado.
  - Limpar as sessões de carrinho, cupom e frete após o sucesso do pedido.
- **Atualizar `App\Views/shop/pedido_sucesso.php` e Detalhes do Pedido**:
  - Exibir o detalhamento de frete e cupom aplicado no resumo do pedido.

---

## Critérios de Aceitação (UAT)
1. **Simulador de Frete**: Na página do produto, ao digitar um CEP válido, o sistema exibe opções de frete (PAC, SEDEX) com prazos estimados e valores calculados.
2. **Frete Grátis**: Produtos marcados com `frete_gratis` ou carrinhos acima de R$ 199 exibem opção de Frete Grátis (R$ 0,00).
3. **Gestão de Cupons**: O administrador consegue criar, editar, desativar e excluir cupons no painel admin com limites de uso e datas de validade.
4. **Aplicação de Cupons**: O cliente consegue aplicar um cupom válido no carrinho; o valor do desconto é subtraído do total e exibido de forma clara. Cupons expirados ou com valor mínimo não atingido exibem mensagem amigável de erro.
5. **Persistência Completa**: Ao finalizar o pedido, os dados de frete e cupom são registrados na tabela `pedidos`, com o estoque baixado e o contador de uso do cupom incrementado.
