---
slug: restricao-compradores-avaliacoes
date: 2026-08-20
status: complete
description: Restringir o envio de avaliações exclusivamente a compradores verificados e administradores
---

# Summary: Restrição de Envio de Avaliações a Compradores Verificados e Administradores

## O que foi realizado:
1. **View `app/Views/shop/produto_detalhe.php`**:
   - Remoção definitiva do card genérico de convite para deslogados.
   - Condição `$podeAvaliar = $isLoggedIn && ($comprou || $isAdmin)`.
   - Exibição do formulário de avaliação somente para compradores verificados ou administradores.
   - Para usuários autenticados sem compra confirmada, exibição do aviso discreto: *"Apenas clientes que adquiriram este produto podem enviar uma avaliação."*
   - Para visitantes deslogados, formulário oculto, exibindo diretamente o feed de avaliações.
2. **Controller `app/Controllers/AvaliacaoController.php`**:
   - Validação estrita no método `enviar()`: se o usuário não for Admin e não possuir pedido pago/entregue do produto, retorna erro com status 403 (AJAX) ou flash message (`Você precisa ter adquirido este produto para avaliá-lo.`).
3. **Validação**:
   - Todos os 55 testes PHPUnit passando com 100% de sucesso.
