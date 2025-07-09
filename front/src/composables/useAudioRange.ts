import { ref, computed, watch, type Ref } from 'vue'
import { usePlayerStore } from '@/stores/player'

// Composable pour gérer la barre de progression audio avec interactions utilisateur
export function useAudioRange(
  audioElement: Ref<HTMLAudioElement | null>,
  currentTime: Ref<number>,
  duration: Ref<number>
) {
  const playerStore = usePlayerStore();
  const rangeValue = ref(0);
  const pendingSeekTime = ref<number | null>(null);

  // Synchroniser le range avec le currentTime quand l'utilisateur n'interagit pas
  watch(currentTime, (newTime) => {
    if (!playerStore.isPlayerInteraction) {
      rangeValue.value = newTime;
    }
  });

  // Stocker la valeur pendant l'interaction, mais ne pas l'appliquer immédiatement
  watch(rangeValue, (newValue) => {
    if (playerStore.isPlayerInteraction) {
      pendingSeekTime.value = newValue;
    }
  });

  // Mise à jour de l'audio à la fin de l'interaction
  watch(() => playerStore.isPlayerInteraction, (isInteracting) => {
    if (!isInteracting && pendingSeekTime.value !== null) {
      if (audioElement.value) {
        audioElement.value.currentTime = pendingSeekTime.value;
        currentTime.value = pendingSeekTime.value;
      }
      pendingSeekTime.value = null;
    }
  });

  const rangeBackground = computed(() => {
    const percentage = duration.value > 0 ? (rangeValue.value / duration.value) * 100 : 0;
    const activeColor = playerStore.isPlayerInteraction ? '#ffffffcc' : '#ffffff';
    return `linear-gradient(90deg, ${activeColor} ${percentage}%, #ffffff2a ${percentage}%)`;
  });

  const handleInteractionStart = () => {
    playerStore.setIsPlayerInteraction(true);
  };

  const handleInteractionEnd = () => {
    playerStore.setIsPlayerInteraction(false);
  };

  return {
    rangeValue,
    rangeBackground,
    isInteracting: computed(() => playerStore.isPlayerInteraction),
    handleInteractionStart,
    handleInteractionEnd,
  };
}
