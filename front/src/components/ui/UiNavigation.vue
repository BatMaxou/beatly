<template>
  <nav class="py-4 border-b border-gray-200 mb-8">
    <ul class="flex flex-wrap gap-4 list-none p-0 m-0">
      <li v-for="(item, index) in navItems" :key="index">
        <a
          href="#"
          class="block px-4 py-2 text-gray-700 rounded transition-all duration-200 ease-in-out hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
          :class="{ 'bg-gray-200 dark:bg-gray-700 font-bold': activeItem === item.id }"
          @click.prevent="selectItem(item.id, item.label)"
        >
          {{ item.label }}
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from "vue";

const props = defineProps({
  activeItem: {
    type: String,
    default: "buttons",
  },
});

const emit = defineEmits(["selectedId", "selectedTitle"]);

const navItems = ref([
  { id: "music", label: "Musique" },
  { id: "search", label: "Recherche" },
  { id: "buttons", label: "Boutons" },
  { id: "menus", label: "Menus" },
  { id: "forms", label: "Formulaires" },
  { id: "cards", label: "Cartes" },
  { id: "modals", label: "Modales" },
  { id: "tables", label: "Tableaux" },
  { id: "alerts", label: "Alertes" },
]);

function selectItem(id, label) {
  emit("selectedId", id);
  emit("selectedTitle", label);
}
</script>
