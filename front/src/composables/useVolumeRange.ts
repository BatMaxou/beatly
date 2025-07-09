import { ref, computed, watch, type Ref } from 'vue'
import { usePlayerStore } from '@/stores/player'
import { usePlayer } from '@/composables/usePlayer'

export function useVolumeRange() {
  const playerStore = usePlayerStore();
  const { setVolume, setMute } = usePlayer();
  
  const volumeValue = ref(playerStore.volume);

  // Synchroniser le volume avec le store quand l'utilisateur n'interagit pas
  watch(() => playerStore.volume, (newVolume) => {
    if (!playerStore.isVolumeInteraction) {
      volumeValue.value = newVolume;
    }
  });

  // Stocker la valeur du volume
  watch(volumeValue, (newValue) => {
    if (playerStore.isVolumeInteraction) {
      setVolume(newValue)
    }
  });

  const volumeBackground = computed(() => {
    const percentage = volumeValue.value;
    const activeColor = playerStore.isVolumeInteraction ? '#ffffffcc' : '#ffffff';
    return `linear-gradient(90deg, ${activeColor} ${percentage}%, #ffffff2a ${percentage}%)`;
  });

  const handleVolumeInteractionStart = () => {
    playerStore.setIsVolumeInteraction(true);
  };

  const handleVolumeInteractionEnd = () => {
    playerStore.setIsVolumeInteraction(false);
  }

  // Gestion du mute/unmute
  const toggleMute = () => {
    setMute(!playerStore.muted);
  };

  return {
    volumeValue,
    volumeBackground,
    isVolumeInteracting: computed(() => playerStore.isVolumeInteraction),
    handleVolumeInteractionStart,
    handleVolumeInteractionEnd,
    toggleMute,
    isMuted: computed(() => playerStore.muted)
  };
}
