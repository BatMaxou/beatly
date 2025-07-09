import { usePlayerStore } from '@/stores/player'

export default {
  mounted(el: HTMLInputElement) {
    if (el.tagName.toLowerCase() !== 'input' || el.type !== 'range') {
      console.warn('L\'element fourni n\'est pas un input range.');
      return;
    }
    
    const playerStore = usePlayerStore();
    
    const hasInteractionMethod = typeof playerStore.setIsPlayerInteraction === 'function';
    
    const handleInteractionStart = () => {
      if (hasInteractionMethod) {
        playerStore.setIsPlayerInteraction(true);
        console.log('Volume interaction started');
      }
    };
    
    const handleInteractionEnd = () => {
      if (hasInteractionMethod) {
        playerStore.setIsPlayerInteraction(false);
        console.log('Volume interaction ended');
      }
    };

    el.addEventListener('mousedown', handleInteractionStart);
    el.addEventListener('mouseup', handleInteractionEnd);
    el.addEventListener('touchstart', handleInteractionStart);
    el.addEventListener('touchend', handleInteractionEnd);
    el.addEventListener('focus', handleInteractionStart);
    el.addEventListener('blur', handleInteractionEnd);

    const handleMouseLeave = () => {
      if (playerStore.isPlayerInteraction) {
        handleInteractionEnd();
      }
    };

    el.addEventListener('mouseleave', handleMouseLeave);

    (el as any)._volumeDirectiveCleanup = () => {
      el.removeEventListener('mousedown', handleInteractionStart);
      el.removeEventListener('mouseup', handleInteractionEnd);
      el.removeEventListener('touchstart', handleInteractionStart);
      el.removeEventListener('touchend', handleInteractionEnd);
      el.removeEventListener('focus', handleInteractionStart);
      el.removeEventListener('blur', handleInteractionEnd);
      el.removeEventListener('mouseleave', handleMouseLeave);
    };
  },

  unmounted(el: HTMLInputElement) {
    if ((el as any)._volumeDirectiveCleanup) {
      (el as any)._volumeDirectiveCleanup();
      delete (el as any)._volumeDirectiveCleanup;
    }
  }
}
