<template>
  <div class="ui-buttons-showcase">
    <h2>Boutons</h2>
    <div class="button-section">
      <h3>Boutons standards</h3>
      <div class="button-row">
        <button class="primary">Bouton primaire</button>
        <button class="secondary">Bouton secondaire</button>
        <button class="tertiary">Bouton tertiaire</button>
        <button disabled>Bouton désactivé</button>
      </div>
    </div>
    
    <div class="button-section">
      <h3>Boutons avec icônes</h3>
      <div class="button-row">
        <button class="primary">
          <span class="icon">+</span>
          Ajouter
        </button>
        <button class="secondary">
          <span class="icon">↓</span>
          Télécharger
        </button>
        <button class="tertiary">
          <span class="icon">✓</span>
          Confirmer
        </button>
      </div>
    </div>
    
    <div class="button-section">
      <h3>Tailles de boutons</h3>
      <div class="button-row">
        <button class="primary small">Petit</button>
        <button class="primary">Moyen (défaut)</button>
        <button class="primary large">Grand</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ui-buttons-showcase {
  padding: 1rem;
}

.button-section {
  margin-bottom: 2rem;
}

.button-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin: 1rem 0;
}

button {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: 1px solid #ccc;
  background-color: #f5f5f5;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}

button:hover {
  background-color: #e0e0e0;
}

button.primary {
  background-color: #3498db;
  color: white;
  border-color: #2980b9;
}

button.primary:hover {
  background-color: #2980b9;
}

button.secondary {
  background-color: #2ecc71;
  color: white;
  border-color: #27ae60;
}

button.secondary:hover {
  background-color: #27ae60;
}

button.tertiary {
  background-color: #e74c3c;
  color: white;
  border-color: #c0392b;
}

button.tertiary:hover {
  background-color: #c0392b;
}

button.small {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

button.large {
  padding: 0.75rem 1.5rem;
  font-size: 1.125rem;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

button:disabled:hover {
  background-color: #f5f5f5;
}

.icon {
  font-size: 1.2em;
  line-height: 1;
}
</style>
