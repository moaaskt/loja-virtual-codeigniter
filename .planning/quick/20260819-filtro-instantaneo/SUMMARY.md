---
status: complete
date: 2026-08-19
slug: filtro-instantaneo
description: "Implementação do Filtro Instantâneo (Eventos automáticos, Debounce 300ms, Sincronização de URL) e correção de MD022 em gsd_commands.md"
---

# Resumo da Tarefa Rápida: Filtro Instantâneo e Correção MD022

## O que foi realizado

1. **Correção do Lint MD022 no [gsd_commands.md](file:///home/moa-dev/projetos/loja-virtual-codeigniter/gsd_commands.md)**:
   - Adicionadas linhas em branco obrigatórias antes e depois de todos os cabeçalhos (`###`), resolvendo a violação da regra `MD022/blanks-around-headings`.

2. **Implementação do Filtro Instantâneo na Vitrine**:
   - **Escuta Automática de Eventos (`change` e `input`)**:
     - Checkboxes de categorias, gêneros e marcas agora disparam a busca AJAX imediatamente ao serem marcados/desmarcados.
     - Lógica inteligente de "Todas" nas categorias: marcar "Todas" desmarca as demais; selecionar qualquer categoria desmarca "Todas"; desmarcar todas restaura "Todas".
     - Sincronização automática entre o painel desktop (sidebar) e mobile (offcanvas).
   - **Técnica de Debounce (300ms)**:
     - Implementado helper `debounce` de 300ms para o campo de busca textual (`#input-busca`) e controles de faixa de preço (sliders e campos numéricos).
     - Evita disparos desnecessários de requisições ao servidor enquanto o usuário digita ou arrasta o slider.
     - Utilização de `AbortController` para cancelar requisições AJAX pendentes caso uma nova filtragem seja disparada.
   - **Sincronização de URL em Tempo Real (`history.pushState` e `popstate`)**:
     - A URL é atualizada dinamicamente com os parâmetros ativos (ex: `/?categorias[]=1&preco_max=500&termo=tenis`), permitindo copiar e compartilhar o link.
     - Suporte à navegação do histórico do navegador (botões Voltar/Avançar via evento `popstate`).
     - Suporte a deep-linking: ao abrir a página com filtros na query string, o JavaScript popula os formulários e aplica os filtros automaticamente.
     - Botão "Limpar tudo" reseta formulários, zera a URL e atualiza a listagem.
   - **Backend Flexível**:
     - [HomeController.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Controllers/HomeController.php) atualizado no método `buscaApi` para aceitar parâmetros tanto no formato array (`categorias[]`) quanto singular (`categoria`, `marca`, `genero`, `termo`, `q`, `busca`).
