---
slug: restricao-compradores-avaliacoes
date: 2026-08-20
status: complete
description: Restringir o envio de avaliações exclusivamente a compradores verificados e administradores
---

# Restringir Envio de Avaliações a Compradores Verificados e Administradores

## Objetivo
- Remover card genérico de convite para login na PDP.
- Exibir formulário de envio de avaliação apenas se o usuário estiver logado E (for comprador verificado OU for Administrador).
- Se estiver logado mas sem compra confirmada, exibir alerta discreto.
- Se não estiver logado, não exibir formulário (mantendo coluna limpa com os comentários).
- Validar estritamente no backend (`AvaliacaoController::enviar`) rejeitando envios não autorizados com HTTP 403 / erro.
