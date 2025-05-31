<template>
    <div class="ui-page">
        <h1>Bibliothèque de composants UI</h1>
        <p class="description">Cette page présente tous les composants UI disponibles dans l'application.</p>
        
        <UiNavigation :active-item="activeComponent" @selectedId="selectComponent" @selectedTitle="selectComponentTitle" />
        
        <div class="ui-component-showcase">
            <component v-if="!isEmptyComponent" :is="currentComponent" />
            <div v-else class="empty-component">
                <p>Aucun composant trouvé pour "{{ activeComponentTitle }}"</p>
                <p class="secondary-text">Nous travaillons à l'ajouter prochainement.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import UiNavigation from '../components/ui/UiNavigation.vue';
import UiButtons from '../components/ui/buttons/UiButtons.vue';
import UiForms from '../components/ui/forms/UiForms.vue';
import UiMusic from '../components/ui/music/UiMusic.vue';

// Composant actif
const activeComponent = ref('buttons');
const activeComponentTitle = ref('Boutons');
const isEmptyComponent = ref(false);

// Changer de composant
const selectComponent = (componentId) => {
  console.log(componentId);
  isEmptyComponent.value = false;
    activeComponent.value = componentId;
};

const selectComponentTitle = (componentTitle) => {
    activeComponentTitle.value = componentTitle;
};

// Composant à afficher en fonction de la sélection
const currentComponent = computed(() => {
    console.log('active component:', activeComponent.value);  
    console.log(isEmptyComponent)
    switch (activeComponent.value) {
      case 'buttons':
        activeComponentTitle.value = 'Boutons';
        return UiButtons;
      case 'forms':
        activeComponentTitle.value = 'Formulaires';
        return UiForms;
      case 'music':
        activeComponentTitle.value = 'Musique';
        return UiMusic;
      default:
        isEmptyComponent.value = true;
        console.log(isEmptyComponent)
        return {};
    }
});
</script>

<style scoped>
.ui-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

h1 {
    margin-bottom: 0.5rem;
}

.description {
    color: #666;
    margin-bottom: 2rem;
}

.ui-component-showcase {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-top: 1rem;
    padding: 1rem;
}

.empty-component {
    text-align: center;
    padding: 3rem 1rem;
    color: #666;
}

.empty-component p {
    margin: 0.5rem 0;
    font-size: 1.1rem;
}

.empty-component .secondary-text {
    font-size: 0.9rem;
    color: #999;
}
</style>