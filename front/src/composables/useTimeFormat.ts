import { computed, type Ref } from 'vue'

export function useTimeFormat(currentTime: Ref<number>, duration: Ref<number>) {
  
  /**
   * Formate un temps en secondes vers le format mm:ss
   */
  const formatTime = (timeInSeconds: number): string => {
    if (isNaN(timeInSeconds) || timeInSeconds < 0) {
      return "0:00";
    }
    
    const minutes = Math.floor(timeInSeconds / 60);
    const seconds = Math.floor(timeInSeconds % 60);
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
  };

  /**
   * Temps actuel formaté
   */
  const formattedCurrentTime = computed(() => {
    return formatTime(currentTime.value);
  });

  /**
   * Durée totale formatée
   */
  const formattedDuration = computed(() => {
    return formatTime(duration.value);
  });

  /**
   * Temps restant formaté
   */
  const formattedRemainingTime = computed(() => {
    const remaining = duration.value - currentTime.value;
    return formatTime(remaining);
  });

  return {
    formatTime,
    formattedCurrentTime,
    formattedDuration,
    formattedRemainingTime
  };
}
