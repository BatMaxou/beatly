import { defineStore } from "pinia";
import { ref } from "vue";

import type { Music } from "@/utils/types";

export const useRecommendationStore = defineStore("recommendations", () => {
  const recommendations = ref<Music[]>([]);

  const setRecommendations = (newRecommendations: Music[]) => {
    recommendations.value = newRecommendations;
  };

  return {
    recommendations,
    setRecommendations,
  };
});
