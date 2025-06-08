<script setup>
import { defineProps, ref } from "vue";
import PlayPauseButton from "@/components/ui/buttons/UniqPlayButton.vue";

const props = defineProps({
  result: {
    type: Object,
    required: true,
  },
});

const isHovered = ref(false);

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

function getTypeLabel() {
  switch (props.result.type) {
    case "album":
      return "Album";
    case "artist":
      return "Artiste";
    case "song":
      return "Chanson";
    default:
      return props.result.type;
  }
}
</script>

<template>
  <div
    class="flex flex-col md:flex-row rounded-lg overflow-hidden shadow-sm transition-all duration-300 ease-in-out cursor-pointer mb-6 w-full md:w-[500px] hover:shadow-md hover:bg-white dark:hover:bg-gray-800 hover:-translate-y-0.5"
    @mousedown.prevent
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <div class="relative w-full md:w-40 md:min-w-40 h-[200px] md:h-40">
      <img :src="result.cover" :alt="result.title" class="w-full h-full object-cover block" />
      <div
        class="absolute inset-0 bg-black/50 flex justify-center items-center opacity-0 hover:opacity-100 transition-opacity duration-300"
      >
        <PlayPauseButton v-if="result.type === 'album' || result.type === 'song'" />
      </div>
    </div>
    <div class="flex flex-col justify-center flex-1 p-4 md:pl-5 md:max-w-[calc(100%-10rem)]">
      <div class="text-xs uppercase font-semibold mb-2 text-blue-500">
        {{ getTypeLabel() }}
      </div>
      <h3
        class="text-2xl md:text-xl font-semibold mb-2 truncate w-full transition-colors duration-200"
        :class="isHovered ? 'text-gray-600' : 'text-white'"
      >
        {{ result.title }}
      </h3>
      <div class="flex flex-wrap gap-0 text-sm">
        <div
          v-if="result.artist"
          class="transition-colors duration-200"
          :class="isHovered ? 'text-gray-600' : 'text-white'"
        >
          {{ result.artist }}
        </div>
        <div
          v-if="result.year"
          class="transition-colors duration-200 relative pl-4"
          :class="isHovered ? 'text-gray-600' : 'text-white'"
        >
          <span
            class="absolute left-1 transition-colors duration-200"
            :class="isHovered ? 'text-gray-600' : 'text-white'"
            >•</span
          >
          {{ result.year }}
        </div>
      </div>
    </div>
  </div>
</template>
