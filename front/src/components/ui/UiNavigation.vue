<template>
  <nav class="ui-navigation">
    <ul>
      <li v-for="(item, index) in navItems" :key="index">
        <a 
          href="#"
          :class="{ active: activeItem === item.id }"
          @click.prevent="selectItem(item.id, item.label)"
        >
          {{ item.label }}
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
  activeItem: {
    type: String,
    default: 'buttons'
  }
});

const emit = defineEmits(['selectedId'], ['selectedTitle']);

const navItems = ref([
  { id: 'music', label: 'Musique' },
  { id: 'buttons', label: 'Boutons' },
  { id: 'forms', label: 'Formulaires' },
  { id: 'cards', label: 'Cartes' },
  { id: 'modals', label: 'Modales' },
  { id: 'tables', label: 'Tableaux' },
  { id: 'alerts', label: 'Alertes' }
]);

function selectItem(id, label) {
  emit('selectedId', id);
  emit('selectedTitle', label);
}
</script>

<style scoped>
.ui-navigation {
  padding: 1rem 0;
  border-bottom: 1px solid #eaeaea;
  margin-bottom: 2rem;
}

.ui-navigation ul {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 1rem;
  flex-wrap: wrap;
}

.ui-navigation a {
  display: block;
  padding: 0.5rem 1rem;
  text-decoration: none;
  color: #333;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.ui-navigation a:hover {
  background-color: #f5f5f5;
}

.ui-navigation a.active {
  background-color: #e0e0e0;
  font-weight: bold;
}
</style>
