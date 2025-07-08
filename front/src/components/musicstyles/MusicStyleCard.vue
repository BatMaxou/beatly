<script setup lang="ts">
import { defineProps, computed } from "vue";

const props = defineProps({
  style: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: "",
  },
  customStyles: {
    type: Object,
    default: () => ({}),
  },
});

// Cette fonction génère le style d'arrière-plan basé sur le genre musical
// A adapter quand les genres seront récupérés depuis l'API avec leurs couleurs / fond respectif.ve.s
const backgroundStyle = computed(() => {
  const gradients: Record<string, string> = {
    rock: "linear-gradient(45deg, #e74c3c, #c0392b)",
    pop: "linear-gradient(45deg, #3498db, #2980b9)",
    jazz: "linear-gradient(45deg, #f1c40f, #f39c12)",
    electronic: "linear-gradient(45deg, #1abc9c, #16a085)",
    hiphop: "linear-gradient(45deg, #9b59b6, #8e44ad)",
    classical: "linear-gradient(45deg, #34495e, #2c3e50)",
    reggae: "linear-gradient(45deg, #2ecc71, #27ae60)",
    rnb: "linear-gradient(45deg, #e67e22, #d35400)",
  };

  // Utiliser le gradient correspondant au style ou un gradient par défaut
  const backgroundImage = gradients[props.style] || "linear-gradient(45deg, #95a5a6, #7f8c8d)";

  return {
    backgroundImage,
    ...props.customStyles,
  };
});
</script>

<template>
  <div
    class="w-full h-20 relative overflow-hidden bg-center bg-cover bg-no-repeat transition-all duration-300 ease-in-out shadow-md"
    :class="style"
    :style="backgroundStyle"
  >
    <div class="absolute inset-0 flex items-end justify-start bg-black/30 text-white">
      <h4 v-if="!customStyles.hideTitle" class="m-0 py-2.5 px-4 text-sm font-bold uppercase">
        {{ label || style }}
      </h4>
    </div>
  </div>
</template>
