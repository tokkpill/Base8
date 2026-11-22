/**
 * Base8 — Search Engine Frontend
 * Real-time search with debounced input and autocomplete
 */
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    let debounceTimer;
    let suggestionsContainer = null;

    // Create suggestions dropdown
    function createSuggestionsContainer() {
        if (suggestionsContainer) return;
        suggestionsContainer = document.createElement('div');
        suggestionsContainer.className = 'search-suggestions';
        suggestionsContainer.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(30, 30, 46, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.08);
            border-top: none;
            border-radius: 0 0 1rem 1rem;
            max-height: 30rem;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        `;
        searchInput.parentElement.style.position = 'relative';
        searchInput.parentElement.appendChild(suggestionsContainer);
    }

    // Fetch and display suggestions
    async function fetchSuggestions(query) {
        if (query.length < 2) {
            if (suggestionsContainer) suggestionsContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            createSuggestionsContainer();

            if (data.length === 0) {
                suggestionsContainer.style.display = 'none';
                return;
            }

            suggestionsContainer.innerHTML = data.map(item => `
                <a href="/${item.tipo === 'asset' ? 'marketplace/asset' : 'asesores/perfil'}?id=${item.referencia_id}" 
                   style="display: flex; align-items: center; gap: 1rem; padding: 1.2rem 2rem; color: #F1F5F9; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 150ms ease;"
                   onmouseover="this.style.background='rgba(16,185,129,0.1)'" 
                   onmouseout="this.style.background='transparent'">
                    <i class="fa-solid ${item.tipo === 'asset' ? 'fa-file' : 'fa-user'}" style="color: #10B981; width: 2rem; text-align: center;"></i>
                    <span style="font-size: 1.4rem;">${item.titulo}</span>
                    <span style="font-size: 1.1rem; color: #64748B; margin-left: auto; text-transform: capitalize;">${item.tipo}</span>
                </a>
            `).join('');

            suggestionsContainer.style.display = 'block';
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    // Debounced search input
    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchSuggestions(e.target.value.trim());
        }, 300);
    });

    // Close suggestions on click outside
    document.addEventListener('click', (e) => {
        if (suggestionsContainer && !searchInput.parentElement.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });

    // Submit on Enter
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            if (suggestionsContainer) suggestionsContainer.style.display = 'none';
        }
    });
});
