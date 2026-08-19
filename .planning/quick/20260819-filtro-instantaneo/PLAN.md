# Quick Task: Filtro Instantâneo e Correção MD022

## Objetivo
1. Explicar e corrigir o problema de lint `MD022/blanks-around-headings` no arquivo `gsd_commands.md`.
2. Implementar o Filtro Instantâneo na vitrine de produtos da loja virtual com:
   - Escuta automática de eventos (`change` em checkboxes de categorias, marcas, gênero; `input` em busca e faixa de preço).
   - Debounce de 300ms para busca textual e controle de preços (sliders e inputs numéricos).
   - Sincronização em tempo real da URL via `history.pushState` / `history.replaceState`, permitindo compartilhamento de links filtrados e suporte à navegação no histórico (`popstate`).

## Arquivos Afetados
- `gsd_commands.md` (correção de espaçamento em volta de títulos)
- `app/Views/shop/index.php` (lógica de filtro instantâneo, debounce, sincronização de URL e manipulação de eventos)
- `app/Views/shop/_filter_panel.php` (ajustes de classes/IDs e sincronização desktop/mobile)
- `app/Controllers/HomeController.php` (compatibilidade ampla de parâmetros de busca/filtro)

## Critérios de Aceite
- [x] `gsd_commands.md` sem violações de `MD022` (todos os títulos com linhas em branco acima e abaixo).
- [x] Checkboxes disparam filtragem AJAX imediatamente ao serem marcados/desmarcados.
- [x] Campos de texto e sliders de preço utilizam debounce de 300ms antes de disparar a requisição.
- [x] URL do navegador é atualizada em tempo real conforme os filtros são alterados.
- [x] Ao carregar a página com parâmetros de filtro na URL ou usar Voltar/Avançar (`popstate`), o estado dos filtros e a listagem de produtos são restaurados.
- [x] O botão "Limpar tudo" reseta os filtros, a URL e a lista de produtos.
