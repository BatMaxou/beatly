<script setup lang="ts">
import { ref, onMounted, nextTick } from "vue";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import arrowRight from "@/assets/icons/arrow-right-light.svg";

interface Props {
  scrollAmount?: number; // Nombre d'éléments à faire défiler
  gap?: number; // Gap entre les éléments en px
  showArrows?: boolean; // Afficher ou masquer les flèches
  arrowClasses?: string; // Classes CSS personnalisées pour les flèches
  inlinePadding?: string;
}

const props = withDefaults(defineProps<Props>(), {
  scrollAmount: 3,
  gap: 32,
  showArrows: true,
  arrowClasses:
    "absolute top-1/2 p-2 transform -translate-y-1/2 bg-black opacity-70 rounded-full shadow-md hover:opacity-90 transition-opacity",
  inlinePadding: "px-10",
});

const scrollContainer = ref<HTMLElement | null>(null);
const showNextButton = ref(true);
const showPrevButton = ref(false);

function scrollForward() {
  if (scrollContainer.value) {
    const elementWidth = scrollContainer.value.firstElementChild?.clientWidth || 0;
    const scrollDistance = (elementWidth + props.gap) * props.scrollAmount;
    scrollContainer.value.scrollBy({ left: scrollDistance, behavior: "smooth" });
  }
}

function scrollBackward() {
  if (scrollContainer.value) {
    const elementWidth = scrollContainer.value.firstElementChild?.clientWidth || 0;
    const scrollDistance = (elementWidth + props.gap) * props.scrollAmount;
    scrollContainer.value.scrollBy({ left: -scrollDistance, behavior: "smooth" });
  }
}

function checkScrollPosition() {
  if (scrollContainer.value) {
    const { scrollLeft, scrollWidth, clientWidth } = scrollContainer.value;
    showNextButton.value = scrollLeft + clientWidth < scrollWidth - 10; // 10px de marge
    showPrevButton.value = scrollLeft > 10;
  }
}

function updateScrollButtons() {
  nextTick(() => {
    checkScrollPosition();
  });
}

defineExpose({
  updateScrollButtons,
  scrollForward,
  scrollBackward,
});

onMounted(() => {
  setTimeout(() => {
    checkScrollPosition();
  }, 100);
});
</script>

<template>
  <div class="relative">
    <div
      ref="scrollContainer"
      :class="'flex flex-row justify-start overflow-x-auto scrollbar-hide ' + inlinePadding"
      :style="{ gap: `${gap}px` }"
      @scroll="checkScrollPosition"
    >
      <slot />
    </div>

    <button
      v-if="showArrows && showPrevButton"
      :class="`${arrowClasses} left-4`"
      @click="scrollBackward"
      aria-label="Défiler vers la gauche"
    >
      <img :src="arrowLeft" class="w-8 h-8" alt="Flèche gauche" />
    </button>

    <button
      v-if="showArrows && showNextButton"
      :class="`${arrowClasses} right-4`"
      @click="scrollForward"
      aria-label="Défiler vers la droite"
    >
      <img :src="arrowRight" class="w-8 h-8" alt="Flèche droite" />
    </button>
  </div>
</template>
