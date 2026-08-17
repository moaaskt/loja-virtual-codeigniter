# Requisitos do Sistema

## Requisitos Existentes e Concluídos
- [x] Login seguro e autenticação de administrador.
- [x] CRUD completo de Produtos e Categorias.
- [x] Upload inicial de imagens.
- [x] Catálogo de produtos (Vitrine) com paginação e página de detalhes.
- [x] Carrinho de compras.
- [x] Checkout inicial.
- [x] Configuração inicial Docker (necessita de ajustes).

## Requisitos a Serem Implementados (Backlog)

### Funcionalidades (Novas e Melhorias)
- [ ] Gestão e baixa automática de estoque por variação de produto (tamanho/cor).
- [ ] Sistema de busca e filtro por categorias na vitrine da loja.
- [ ] Visualização detalhada dos pedidos realizados no painel do cliente.
- [ ] Visualização detalhada dos pedidos no painel admin.
- [ ] Auditoria e teste em todo o fluxo de Carrinho de Compras e Checkout (criação de pedidos e persistência no banco).
- [ ] Garantir o upload e exibição correta das imagens de produtos e variações no painel Admin.

### Requisitos Técnicos & Segurança
- [ ] Resolver as dívidas técnicas mapeadas no `CONCERNS.md` (Correção da imagem PHP no Docker, configuração do Apache, etc).
- [ ] Validar segurança e filtros de Autenticação (proteger rotas administrativas de usuários normais).
