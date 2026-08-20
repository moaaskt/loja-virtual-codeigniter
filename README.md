# 🛒 G'Store — Loja Virtual em CodeIgniter 4

![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4-EF4223?logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-brightgreen)
![Status](https://img.shields.io/badge/status-em%20evolu%C3%A7%C3%A3o-blue)

E-commerce full-stack completo desenvolvido com **PHP 8 + CodeIgniter 4**, cobrindo todo o fluxo de compra — da vitrine ao pagamento — com painel administrativo robusto, gateway de pagamento (Pix e Cartão), sistema de frete e cupons, avaliações de produtos, notificações por e-mail e dashboard analítico com relatórios.

---

## 📑 Índice

- [Visão Geral](#-visão-geral)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#️-tecnologias)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação e Setup](#-instalação-e-setup)
- [Acessos Padrão](#-acessos-padrão)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Testes](#-testes)
- [Licença](#-licença)

---

## 🎯 Visão Geral

Projeto de estudo aprofundado do framework CodeIgniter 4, implementando um e-commerce real com:

- **Vitrine pública** com catálogo, busca em tempo real, filtros instantâneos e carrinho de compras
- **Área do cliente** com autenticação, checkout completo, pagamento e histórico de pedidos
- **Painel administrativo** com gestão completa de produtos, pedidos, cupons, avaliações, relatórios e muito mais
- **Arquitetura MVC** limpa, com camada de **Services** para regras de negócio complexas

---

## ✨ Funcionalidades

### 🏪 Vitrine (Frente de Loja)

| Módulo | Descrição |
|---|---|
| **Catálogo de Produtos** | Listagem com paginação, imagem principal e galeria de fotos |
| **Busca em Tempo Real** | Campo de busca com AJAX, debounce de 300ms e sincronização de URL via querystring |
| **Filtros Instantâneos** | Filtros por categoria, marca, gênero e faixa de preço com atualização automática |
| **Página de Detalhes (PDP)** | Visualização completa do produto com galeria, variações/SKUs, cálculo de frete e avaliações de clientes |
| **Variações e SKUs** | Sistema genérico de variações (tamanho, cor, voltagem, etc.) com preço e estoque individual por SKU |
| **Carrinho de Compras** | Adicionar, remover, atualizar quantidades, selecionar variações, aplicar cupom e calcular frete |
| **Cálculo de Frete** | Simulação de frete por CEP com múltiplas modalidades (PAC, SEDEX, Expresso) |
| **Cupons de Desconto** | Aplicação de cupons com regras de valor mínimo, validade, uso máximo e tipo (% ou fixo) |
| **Checkout Completo** | Fluxo de finalização com endereço de entrega, resumo do pedido e seleção de pagamento |
| **Gateway de Pagamento** | Pagamento via **Pix** (QR Code / Copia e Cola) e **Cartão de Crédito** com webhook de confirmação |
| **Avaliações e Reviews** | Sistema de classificação por estrelas com restrição a compradores verificados |
| **Área do Cliente** | Histórico de pedidos com status em tempo real |

### 🔐 Autenticação

| Módulo | Descrição |
|---|---|
| **Login / Registro** | Autenticação com hash seguro (`password_hash`) e controle de sessão |
| **Filtros de Rota** | Proteção de rotas com filtros `auth`, `guest` e `admin` |
| **Controle de Acesso** | Separação de papéis entre clientes e administradores |

### ⚙️ Painel Administrativo

| Módulo | Descrição |
|---|---|
| **Dashboard** | Painel inicial com visão geral da loja |
| **Gestão de Produtos** | CRUD completo com upload de imagens, galeria, variações/SKUs dinâmicas por categoria e soft delete com lixeira |
| **Gestão de Categorias** | CRUD de categorias com configuração de tipos de variação |
| **Gestão de Pedidos** | Listagem, detalhes, atualização de status (Pendente → Pago → Enviado → Entregue), código de rastreio e reenvio de e-mails |
| **Gestão de Clientes** | Listagem de clientes com visualização de perfil e ativação/desativação de conta |
| **Cupons de Desconto** | CRUD de cupons com regras de desconto (percentual ou fixo), validade, valor mínimo e limite de uso |
| **Moderação de Avaliações** | Aprovação, rejeição e exclusão de reviews de clientes |
| **Notificações por E-mail** | Envio transacional via SMTP (confirmação de pedido, pagamento aprovado, envio, entrega) com preview de templates e fila resiliente |
| **Fila de Notificações** | Gerenciamento da fila de e-mails com reprocessamento de falhas |
| **Trilha de Auditoria** | Log completo de todas as operações administrativas com registro de IP, ação e dados alterados |
| **Relatórios e BI** | Dashboard analítico com KPIs (faturamento, ticket médio, taxa de conversão), gráficos via Chart.js, relatórios detalhados de vendas, produtos, clientes e cupons com paginação e exportação CSV |

---

## 🛠️ Tecnologias

| Camada | Tecnologia |
|---|---|
| **Linguagem** | PHP 8.1 |
| **Framework** | CodeIgniter 4 |
| **Banco de Dados** | MySQL 8.0 |
| **Front-end** | HTML5, CSS3, Bootstrap 5, JavaScript (Fetch/AJAX) |
| **Gráficos** | Chart.js |
| **Infraestrutura** | Docker + Docker Compose |
| **Servidor Web** | Apache 2 (mod_rewrite) |
| **Gerenciador de Pacotes** | Composer |
| **Testes** | PHPUnit 10 + Faker |

---

## 📋 Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) e [Docker Compose](https://docs.docker.com/compose/install/) instalados
- Git

---

## 🚀 Instalação e Setup

### 1. Clone o repositório

```bash
git clone https://github.com/moaaskt/loja-virtual-codeigniter.git
cd loja-virtual-codeigniter
```

### 2. Configure o ambiente

O arquivo `.env` já está configurado para funcionar com Docker. Revise se necessário:

```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/'

database.default.hostname = db
database.default.database = loja_virtual
database.default.username = user_loja
database.default.password = password_loja
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 3. Suba os containers

```bash
docker compose up -d --build
```

Isso irá iniciar dois containers:
- **`loja_web`** — Apache + PHP 8.1 na porta `80`
- **`loja_db`** — MySQL 8.0 na porta `3306`

### 4. Instale as dependências

```bash
docker exec -it loja_web composer install
```

### 5. Execute as migrations e seeders

```bash
# Criar as tabelas
docker exec -it loja_web php spark migrate

# Criar o usuário administrador padrão
docker exec -it loja_web php spark db:seed AdminUserSeeder
```

### 6. Acesse a aplicação

- **Loja (Vitrine):** [http://localhost](http://localhost)
- **Painel Admin:** [http://localhost/admin/dashboard](http://localhost/admin/dashboard)

---

## 🔑 Acessos Padrão

| Perfil | E-mail | Senha |
|---|---|---|
| **Administrador** | `admin@admin.com` | `123456` |

> Para criar clientes, utilize a tela de registro na vitrine da loja.

---

## 📁 Estrutura do Projeto

```
loja-virtual-codeigniter/
├── app/
│   ├── Commands/              # Comandos CLI personalizados
│   ├── Config/                # Configurações (Routes, Filters, Database, Email)
│   ├── Controllers/
│   │   ├── Admin/             # Controllers do painel administrativo
│   │   │   ├── AdminController.php        # Dashboard
│   │   │   ├── ProdutosController.php     # CRUD de produtos
│   │   │   ├── CategoriasController.php   # CRUD de categorias
│   │   │   ├── PedidoController.php       # Gestão de pedidos
│   │   │   ├── CuponsController.php       # Gestão de cupons
│   │   │   ├── AvaliacoesController.php   # Moderação de reviews
│   │   │   ├── RelatoriosController.php   # Relatórios e BI
│   │   │   ├── AuditoriaController.php    # Trilha de auditoria
│   │   │   └── ...
│   │   ├── HomeController.php             # Vitrine e busca
│   │   ├── CarrinhoController.php         # Carrinho de compras
│   │   ├── PedidoController.php           # Checkout e pedidos
│   │   ├── PagamentoController.php        # Gateway de pagamento
│   │   ├── FreteController.php            # Cálculo de frete
│   │   ├── AvaliacaoController.php        # Avaliações de produtos
│   │   ├── AuthController.php             # Login e registro
│   │   └── WebhookController.php          # Webhooks de pagamento
│   ├── Database/
│   │   ├── Migrations/        # 22 migrations (esquema completo)
│   │   └── Seeds/             # Seeders (admin, cupons, dados de teste)
│   ├── Filters/               # Filtros de autenticação e autorização
│   ├── Helpers/               # Funções auxiliares
│   ├── Libraries/             # Bibliotecas personalizadas
│   ├── Models/                # Modelos Eloquent-style do CI4
│   ├── Services/              # Camada de serviços (regras de negócio)
│   │   ├── CarrinhoService.php
│   │   ├── PedidoService.php
│   │   ├── PagamentoService.php
│   │   ├── FreteService.php
│   │   ├── EmailService.php
│   │   ├── AuditService.php
│   │   └── RelatorioService.php
│   └── Views/
│       ├── shop/              # Views da vitrine
│       ├── admin/             # Views do painel administrativo
│       ├── auth/              # Views de login e registro
│       ├── cliente/           # Views da área do cliente
│       ├── emails/            # Templates de e-mail transacional
│       └── layouts/           # Layouts base (admin e público)
├── public/
│   ├── assets/                # CSS, JS e imagens estáticas
│   └── uploads/               # Imagens de produtos (upload)
├── tests/                     # Testes automatizados (PHPUnit)
├── docker-compose.yml         # Orquestração dos containers
├── Dockerfile                 # Imagem PHP 8.1 + Apache
├── composer.json              # Dependências PHP
└── banco_loja.sql             # Dump do banco de dados
```

---

## 🧪 Testes

O projeto utiliza **PHPUnit 10** com **68 testes** e **632 asserções** aprovadas.

```bash
# Rodar os testes dentro do container
docker exec -it loja_web php vendor/bin/phpunit
```

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
