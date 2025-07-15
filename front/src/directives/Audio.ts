import { usePlayerPreparation } from "@/composables/usePlayerPreparation";
import { useToast } from "@/composables/useToast";
import { useApiClient } from "@/stores/api-client";
import { usePlayerStore } from "@/stores/player";
import { streamToAudioUrl } from "@/utils/stream";

// Fonction de throttle généré par Claude Sonnet 4 pour optimiser les appels de fonction
function throttle<T extends (...args: any[]) => any>(func: T, limit: number): T {
  let inThrottle: boolean;
  return ((...args: any[]) => {
    if (!inThrottle) {
      func(...args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  }) as T;
}

interface ExtendedHTMLAudioElement extends HTMLAudioElement {
  _audioDirectiveCleanup?: () => void;
}

export default {
  mounted(el: HTMLAudioElement) {
    if (el.tagName.toLowerCase() !== "audio") {
      console.warn("L'element fourni n'est pas un element audio.");
      return;
    }

    const playerStore = usePlayerStore();
    const { apiClient } = useApiClient();
    const { playNextSong, loadRandomQueue, storeAdjacentMusicInQueue } = usePlayerPreparation();
    const { showError } = useToast();
    const extendedEl = el as ExtendedHTMLAudioElement;

    el.controls = true;

    const handleTimeUpdate = throttle(() => {
      // Mettre à jour le store seulement 5 fois par seconde pour éviter les appels excessifs
      if (!playerStore.isPlayerInteraction) {
        playerStore.setCurrentTime(el.currentTime);
      }
    }, 200); // 200ms = 5 fois par seconde

    const handleLoadedMetadata = () => {
      playerStore.setDuration(el.duration);
    };

    const handleDurationChange = () => {
      if (el) {
        playerStore.setDuration(el.duration);
      }
    };

    const handleCanPlay = () => {
      if (el) {
        playerStore.setDuration(el.duration);
      }
    };

    const handleEnded = async () => {
      if (playerStore.nextMusic) {
        playNextSong();
      } else {
        if (playerStore.isRepeatQueue) {
          if (playerStore.isRandomQueue) {
            const randomQueue = await loadRandomQueue(0);
            const firstElement = randomQueue?.queueItems[0];
            if (firstElement) {
              const queueFile = playerStore.queueFile?.find(
                (item) => item.musicId === firstElement.music.id,
              );
              if (queueFile) {
                await playerStore.setListen(firstElement.music, queueFile.file, 1);
              } else {
                await apiClient.music.getFile(firstElement.music.id).then(async (response) => {
                  if (response) {
                    await playerStore.setListen(
                      firstElement.music,
                      await streamToAudioUrl(response),
                      1,
                    );
                  } else {
                    showError("Ce titre n'est pas disponible");
                  }
                });
              }
            }
          } else {
            const firstElement = playerStore.queue?.queueItems[0];
            if (firstElement) {
              const queueFile = playerStore.queueFile?.find(
                (item) => item.musicId === firstElement.music.id,
              );
              if (queueFile) {
                await playerStore.setListen(firstElement.music, queueFile.file, 1);
                storeAdjacentMusicInQueue();
              } else {
                await apiClient.music.getFile(firstElement.music.id).then(async (response) => {
                  if (response) {
                    await playerStore.setListen(
                      firstElement.music,
                      await streamToAudioUrl(response),
                      1,
                    );
                    storeAdjacentMusicInQueue();
                  } else {
                    showError("Ce titre n'est pas disponible");
                  }
                });
              }
            }
          }
        } else {
          playerStore.setPause();
        }
      }
    };

    el.addEventListener("loadedmetadata", handleLoadedMetadata);
    el.addEventListener("timeupdate", handleTimeUpdate);
    el.addEventListener("durationchange", handleDurationChange);
    el.addEventListener("canplay", handleCanPlay);
    el.addEventListener("ended", handleEnded);

    extendedEl._audioDirectiveCleanup = () => {
      el.removeEventListener("loadedmetadata", handleLoadedMetadata);
      el.removeEventListener("timeupdate", handleTimeUpdate);
      el.removeEventListener("durationchange", handleDurationChange);
      el.removeEventListener("canplay", handleCanPlay);
      el.removeEventListener("ended", handleEnded);
    };
  },

  unmounted(el: HTMLAudioElement) {
    const extendedEl = el as ExtendedHTMLAudioElement;
    if (extendedEl._audioDirectiveCleanup) {
      extendedEl._audioDirectiveCleanup();
      delete extendedEl._audioDirectiveCleanup;
    }
  },
};
