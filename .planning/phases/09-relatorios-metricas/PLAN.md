# Plano de Execução — Phase 9: Relatórios e Métricas Avançadas no Painel Admin

## Objetivo
Desenvolver e integrar um módulo completo de **Business Intelligence (BI), Métricas e Relatórios Analíticos** no Painel Administrativo da Loja Virtual CodeIgniter. Esse módulo oferecerá aos gestores visão clara do desempenho de vendas, faturamento líquido, ticket médio, taxa de conversão, gráficos interativos com Chart.js, rankings detalhados de produtos e clientes, monitoramento de cupons de desconto e exportação de dados em formato CSV compatível com Excel/Google Sheets.

---

## Detalhamento das Tarefas de Implementação

### 1. Camada de Serviço (`App\Services\RelatorioService`)

- **Arquivo**: `app/Services/RelatorioService.php`
- **Funcionalidades e Métodos**:
  - `getPeriodoDatas(?string $periodo = '30d', ?string $dataInicio = null, ?string $dataFim = null): array`:
    - Normaliza períodos pré-definidos: `hoje`, `7d`, `30d`, `mes_atual`, `ano_atual` ou `custom`.
    - Retorna `['inicio' => 'YYYY-MM-DD 00:00:00', 'fim' => 'YYYY-MM-DD 23:59:59', 'label' => '...']`.
  - `getKpis(string $dataInicio, string $dataFim): array`:
    - `faturamento_total`: Soma de `valor_total` para pedidos com status em (`pago`, `enviado`, `entregue`).
    - `total_pedidos`: Contagem total de pedidos no período.
    - `pedidos_pagos`: Contagem de pedidos pagos/concluídos.
    - `taxa_conversao`: Percentual de pedidos pagos sobre o total.
    - `ticket_medio`: Faturamento total / pedidos pagos.
    - `novos_clientes`: Contagem de usuários com role `cliente` cadastrados no período.
    - `total_descontos`: Soma de `desconto_valor` em pedidos no período.
    - `total_frete`: Soma de `frete_valor` em pedidos no período.
  - `getEvolucaoVendas(string $dataInicio, string $dataFim, string $agrupamento = 'dia'): array`:
    - Retorna `labels` (ex: `15/08`, `16/08` ou `Jan/26`) e séries de dados para `faturamento` e `pedidos`.
  - `getVendasPorFormaPagamento(string $dataInicio, string $dataFim): array`:
    - Agrupa pedidos por `forma_pagamento` (Pix, Cartão de Crédito, etc.) com total faturado, quantidade e percentual.
  - `getFaturamentoPorCategoria(string $dataInicio, string $dataFim): array`:
    - Faz join entre `pedidos`, `pedido_produtos`, `produtos` e `categorias` para calcular faturamento e itens vendidos por categoria.
  - `getStatusDistribuicao(string $dataInicio, string $dataFim): array`:
    - Contagem de pedidos por status (`pendente`, `pago`, `enviado`, `entregue`, `cancelado`).
  - `getTopProdutos(string $dataInicio, string $dataFim, int $limit = 10): array`:
    - Ranking dos produtos mais vendidos: ID, nome, imagem, categoria, total de itens vendidos, receita total gerada e estoque atual.
  - `getTopClientes(string $dataInicio, string $dataFim, int $limit = 10): array`:
    - Ranking dos clientes com maior volume financeiro: ID, nome, email, total de pedidos, total gasto, ticket médio e data da última compra.
  - `getRelatorioCupons(string $dataInicio, string $dataFim): array`:
    - Análise de uso de cupons: código, quantidade de utilizações, total faturado com o cupom e total de desconto concedido.
  - `getVendasDetalhadas(string $dataInicio, string $dataFim, array $filtros = []): array`:
    - Listagem de pedidos com dados do cliente, itens, frete, desconto, valor final, forma de pagamento e status.
  - `gerarCsv(string $tipo, string $dataInicio, string $dataFim, array $filtros = []): string`:
    - Constrói o conteúdo CSV com BOM UTF-8 (`\xEF\xBB\xBF`), cabeçalhos em português, formatação monetária e datas no formato brasileiro `d/m/Y H:i`.

---

### 2. Controladores & Rotas Administrativas

- **Arquivo**: `app/Controllers/Admin/RelatoriosController.php`
- **Rotas** (dentro do grupo `admin` em `app/Config/Routes.php`):
  - `GET admin/relatorios` -> `index()` (Dashboard analítico e visão geral).
  - `GET admin/relatorios/vendas` -> `vendas()` (Relatório tabular de vendas).
  - `GET admin/relatorios/produtos` -> `produtos()` (Relatório de produtos e giro de estoque).
  - `GET admin/relatorios/clientes` -> `clientes()` (Relatório de clientes e compras).
  - `GET admin/relatorios/cupons` -> `cupons()` (Relatório de uso de cupons).
  - `GET admin/relatorios/exportar/(:segment)` -> `exportar($tipo)` (Gera download do arquivo CSV com headers HTTP adequados).

---

### 3. Interface do Usuário (Views & Menu Admin)

- **Atualização do Menu Admin** (`app/Views/layouts/admin.php`):
  - Adicionar o menu "Relatórios" com ícone `bi-graph-up-arrow` e links para Visão Geral, Vendas, Produtos, Clientes e Cupons.
- **Views**:
  - `app/Views/admin/relatorios/index.php`: Dashboard com seletores de período, 4 cards de KPIs com indicadores visuais, 4 gráficos Chart.js refinados (Evolução de Vendas, Formas de Pagamento, Categorias e Status) e tabelas resumo.
  - `app/Views/admin/relatorios/vendas.php`: Relatório de vendas com filtros e exportação.
  - `app/Views/admin/relatorios/produtos.php`: Relatório de produtos com ranking de faturamento e estoque.
  - `app/Views/admin/relatorios/clientes.php`: Relatório de clientes com total de compras e ticket médio.
  - `app/Views/admin/relatorios/cupons.php`: Relatório de desempenho de cupons.

---

### 4. Testes Automatizados

- **Arquivo**: `tests/app/RelatorioTest.php`
- **Casos de Teste**:
  - `testCalculoKpisComPedidosValidos`: Verifica faturamento, ticket médio e contagem de pedidos.
  - `testEvolucaoVendasAgrupamento`: Verifica agrupamento temporal diário.
  - `testVendasPorFormaPagamento`: Verifica separação entre Pix e Cartão de Crédito.
  - `testTopProdutosERankings`: Verifica ordenação e agregação de produtos mais vendidos.
  - `testTopClientes`: Verifica cálculo de LTV e total gasto por cliente.
  - `testGeracaoCsvComBOM`: Verifica que a saída de exportação contém BOM UTF-8 e cabeçalhos válidos.
  - `testProtecaoRotasAdmin`: Garante que as rotas de relatórios exigem autenticação de administrador.

---

## Verificação e Critérios de Aceite
1. Todos os 55 testes existentes + novos testes de relatórios passando sem falhas.
2. Navegação completa e responsiva no painel de relatórios.
3. Gráficos Chart.js renderizando com dados dinâmicos e sem erros no console.
4. Downloads de CSV funcionando com codificação UTF-8 e acentuação preservada.
