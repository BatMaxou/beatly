import { inject } from 'vue';

// Clé unique pour l'injection - doit correspondre à celle dans SearchProvider.vue
export const SearchSymbol = Symbol('search');

/**
 * Hook pour utiliser le contexte de recherche dans un composant
 * @returns {Object} Contexte de recherche avec searchQuery, searchResults...
 */
export function useSearch() {
  const context = inject(SearchSymbol);
  if (!context) {
    throw new Error('useSearch doit être utilisé dans un composant enfant de SearchProvider');
  }
  return context;
}
