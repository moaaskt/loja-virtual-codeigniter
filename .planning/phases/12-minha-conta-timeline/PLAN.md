# Plano de Execução — Phase 12: Painel "Minha Conta" & Timeline Visual de Pedidos

## Objetivo
Criar uma **Central do Cliente (Minha Conta) completa, moderna e modular**, equipada com histórico e detalhes de pedidos com **Timeline Visual de Rastreamento de Status**, **Gestão de Múltiplos Endereços com busca automática via ViaCEP** integrada ao Checkout, e **Painel de Perfil & Segurança**.

---

## 🏗️ 1. Banco de Dados & Modelagem

### Migration: `app/Database/Migrations/2026-08-21-060000_CreateClienteEnderecosTable.php`
- Tabela `cliente_enderecos`:
  - `id` (INT AUTO_INCREMENT PK)
  - `usuario_id` (INT FK -> usuarios.id)
  - `titulo` (VARCHAR 60, ex: "Casa", "Trabalho", "Apartamento")
  - `destinatario` (VARCHAR 128)
  - `cep` (VARCHAR 10)
  - `logradouro` (VARCHAR 255)
  - `numero` (VARCHAR 30)
  - `complemento` (VARCHAR 100, nullable)
  - `bairro` (VARCHAR 100)
  - `cidade` (VARCHAR 100)
  - `uf` (VARCHAR 2)
  - `padrao` (TINYINT 1, default 0)
  - `criado_em` (DATETIME)
  - `atualizado_em` (DATETIME, nullable)

### Model: `app/Models/ClienteEnderecoModel.php`
- Métodos para buscar endereços por usuário, definir endereço padrão exclusivo, salvar e remover com segurança.

---

## 🧭 2. Rotas & Controller do Cliente

### Arquivo: `app/Config/Routes.php`
- Rotas agrupadas em `minha-conta` com filtro `auth`:
  - `get('/', 'ClienteController::index')` (Dashboard / Meus Pedidos)
  - `get('pedidos', 'ClienteController::pedidos')`
  - `get('pedidos/(:num)', 'ClienteController::detalhesPedido/$1')` (Timeline & Detalhes)
  - `get('enderecos', 'ClienteController::enderecos')` (Gestão de Endereços)
  - `post('enderecos/salvar', 'ClienteController::salvarEndereco')`
  - `post('enderecos/padrao/(:num)', 'ClienteController::definirPadrao/$1')`
  - `post('enderecos/excluir/(:num)', 'ClienteController::excluirEndereco/$1')`
  - `get('perfil', 'ClienteController::perfil')` (Dados & Segurança)
  - `post('perfil/salvar', 'ClienteController::salvarPerfil')`
  - `post('perfil/trocar-senha', 'ClienteController::trocarSenha')`

### Arquivo: `app/Controllers/ClienteController.php`
- Implementação completa de todos os métodos com validações de dados e mensagens flash de feedback.

---

## 🎨 3. Views da Central do Cliente

- **`app/Views/cliente/_sidebar.php`:**
  - Sidebar elegante com avatar do cliente, nome, e-mail, badges de status e navegação fluida entre seções.
- **`app/Views/cliente/index.php` (ou `meus_pedidos.php`):**
  - Grid/Lista de pedidos com cards informativos, badges de status com cores semânticas, valor total e botão "Ver Detalhes".
- **`app/Views/cliente/detalhes_pedido.php`:**
  - **Timeline Visual de 5 Etapas:** Pedido Realizado ➔ Pagamento Confirmado ➔ Em Separação ➔ Enviado (com código de rastreio) ➔ Entregue.
  - Tabela detalhada de itens com foto, SKU, atributos, quantidade e botão de avaliação com 1 clique.
  - Card de Endereço de Entrega & Pagamento.
- **`app/Views/cliente/enderecos.php`:**
  - Grid de endereços com tag "Padrão" destacada.
  - Modal / Formulário com busca automática de CEP (ViaCEP) e validação em tempo real.
- **`app/Views/cliente/perfil.php`:**
  - Edição de nome, e-mail e formulário de troca de senha com confirmação.

---

## 🛒 4. Integração no Checkout

### Arquivo: `app/Views/shop/checkout.php`
- Exibir seletor rápido de endereços salvos para clientes logados que já possuam endereços cadastrados, autopreenchendo os campos de entrega.

---

## 🧪 5. Testes Automatizados

### Arquivo: `tests/app/MinhaContaETimelineTest.php`
- `testAcessarPainelMinhaContaEPedidos`: Valida carregamento da listagem de pedidos.
- `testVisualizarDetalhesPedidoComTimeline`: Valida o endpoint de detalhes do pedido com timeline visual e itens.
- `testCrudEnderecosClienteComViaCep`: Valida criação, edição, definição de padrão e exclusão de múltiplos endereços.
- `testAtualizarPerfilETrocarSenha`: Valida alteração de nome e atualização de senha criptografada.
