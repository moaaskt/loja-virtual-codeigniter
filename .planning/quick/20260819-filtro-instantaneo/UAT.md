# UAT: Filtro Instantâneo

## Status: Completed

### Test Cases

1. **Instant Filters (Checkboxes)**
   - [x] Checkboxes trigger immediate AJAX search without needing a submit button.
   - [x] Marking "Todas" deselects other categories. Selecting a specific category deselects "Todas".
   - [x] Selections sync correctly between desktop sidebar and mobile offcanvas.

2. **Debounce and Text Search**
   - [x] Typing in the text search or sliding the price range waits 300ms before triggering the search.
   - [x] Rapid changes abort previous requests and only apply the final one.

3. **URL Synchronization & Deep Linking**
   - [x] The URL updates dynamically as filters are applied (e.g. `/?categorias[]=1`).
   - [x] The "Clear all" button resets everything and clears the URL.
   - [x] Copying a URL with filters and opening it in a new tab correctly applies the filters on load.
   - [x] Using Browser Back/Forward buttons properly reflects the previous/next filter states on the UI and results.
