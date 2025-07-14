import { useApiClient } from "@/stores/api-client";
import { usePlayerStore } from "@/stores/player";
import { streamToAudioUrl } from "@/utils/stream";
import type { Music } from "@/utils/types";

export function usePlayerPreparation() {
  const playerStore = usePlayerStore();
  const { apiClient } = useApiClient();

  /**
   * Lancement initial de la musique.
   * Execution de storeAdjacentMusicInQueue pour définir les titres précédents et suivants.
   *
   */

  /**
   * Définit les titres précédents et suivants dans le store du player
   */
  const storeAdjacentMusicInQueue = async () => {
    let previousMusic: Music | null = null;
    let nextMusic = null;
    if (!playerStore.queue) return;

    const queueItems = Object.values(playerStore.queue.queueItems);
    if (queueItems.length === 0) return;

    const previousInQueue = queueItems.find((item) => item.position === playerStore.position - 1);
    const nextInQueue = queueItems.find((item) => item.position === playerStore.position + 1);

    if (previousInQueue) {
      if (playerStore.queueFile) {
        const previousMusicFile = playerStore.queueFile.find(
          (item) => item.musicId === previousInQueue.music.id,
        );
        if (previousMusicFile) {
          previousMusic = previousInQueue.music;
          playerStore.setPreviousMusic(previousMusic);
          playerStore.setPreviousMusicFile(previousMusicFile.file);
        } else {
          previousMusic = previousInQueue.music;
          playerStore.setPreviousMusic(previousInQueue.music);
          playerStore.setPreviousMusicFile(null);
        }
      } else {
        const previousMusicFile = await apiClient.music.getFile(previousInQueue.music.id);
        if (previousMusicFile) {
          previousMusic = previousInQueue.music;
          playerStore.setPreviousMusic(previousMusic);
          playerStore.setPreviousMusicFile(await streamToAudioUrl(previousMusicFile));
        } else {
          previousMusic = previousInQueue.music;
          playerStore.setPreviousMusic(previousInQueue.music);
          playerStore.setPreviousMusicFile(null);
        }
      }
    } else {
      playerStore.setPreviousMusic(null);
      playerStore.setPreviousMusicFile(null);
    }

    if (nextInQueue) {
      if (playerStore.queueFile) {
        const nextMusicFile = playerStore.queueFile.find(
          (item) => item.musicId === nextInQueue.music.id,
        );

        if (nextMusicFile) {
          nextMusic = nextInQueue.music;
          playerStore.setNextMusic(nextMusic);
          playerStore.setNextMusicFile(nextMusicFile.file);
        } else {
          nextMusic = nextInQueue.music;
          playerStore.setNextMusic(nextInQueue.music);
          playerStore.setNextMusicFile(null);
        }
      } else {
        const nextMusicFile = await apiClient.music.getFile(nextInQueue.music.id);
        if (nextMusicFile) {
          nextMusic = nextInQueue.music;
          playerStore.setNextMusic(nextMusic);
          playerStore.setNextMusicFile(await streamToAudioUrl(nextMusicFile));
        } else {
          nextMusic = nextInQueue.music;
          playerStore.setNextMusic(nextInQueue.music);
          playerStore.setNextMusicFile(null);
        }
      }
    } else {
      playerStore.setNextMusic(null);
      playerStore.setNextMusicFile(null);
    }
    return {
      previousMusic,
      nextMusic,
    };
  };

  /**
   * Charge toutes les musiques une à une dans le store du player
   * pour préparer la lecture.
   */
  const loadQueueFile = async () => {
    if (!playerStore.queue) return null;
    const queueItems = Object.values(playerStore.queue.queueItems);
    if (queueItems.length === 0) return null;
    const queueFiles = [];
    for (const music of queueItems) {
      if (
        music.music.id &&
        playerStore.nextMusic?.id === music.music.id &&
        playerStore.nextMusicFile
      ) {
        // Si la musique est équivalente a la suivante, on la récupère dans le store
        // Si la musique est déjà dans le store, on ne la charge pas
        queueFiles.push({ file: playerStore.nextMusicFile, musicId: music.music.id });
      } else if (
        music.music.id &&
        playerStore.previousMusic?.id === music.music.id &&
        playerStore.previousMusicFile
      ) {
        // Si la musique est équivalente a la précédente, on la récupère dans le store
        // Si la musique est déjà dans le store, on ne la charge pas
        queueFiles.push({ file: playerStore.previousMusicFile, musicId: music.music.id });
      } else if (
        music.music.id &&
        playerStore.currentMusic?.id === music.music.id &&
        playerStore.musicFile
      ) {
        // Si la musique est équivalente a la courante, on la récupère depuis store currentMusicFile
        // Si la musique est déjà dans le store, on ne la charge pas
        queueFiles.push({ file: playerStore.musicFile, musicId: music.music.id });
      } else if (music.music.id) {
        // Sinon, on la charge depuis l'API
        const response = await apiClient.music.getFile(music.music.id);
        if (response) {
          queueFiles.push({ file: await streamToAudioUrl(response), musicId: music.music.id });
        }
      }
    }
    // Filtrer les éléments qui ont un file non-null
    const validQueueFiles = queueFiles.filter(
      (item): item is { file: string; musicId: number } => item.file !== null,
    );
    return validQueueFiles.length > 0 ? validQueueFiles : null;
  };

  /**
   * Lecture du titre suivant dans la queue.
   * Si le titre suivant n'existe pas, on arrête la lecture.
   */
  const playNextSong = () => {
    if (playerStore.nextMusic !== null && playerStore.nextMusicFile !== null) {
      playerStore.setListen(
        playerStore.nextMusic,
        playerStore.nextMusicFile,
        playerStore.position + 1,
      );
      storeAdjacentMusicInQueue();
    } else {
      playerStore.setPause();
      return;
    }
  };

  /**
   * Lecture du titre précédent dans la queue.
   * Si le titre précédent n'existe pas, on rejoue le titre.
   */
  const playPreviousSong = () => {
    if (playerStore.previousMusic !== null && playerStore.previousMusicFile !== null) {
      playerStore.setListen(
        playerStore.previousMusic,
        playerStore.previousMusicFile,
        playerStore.position - 1,
      );
      storeAdjacentMusicInQueue();
    } else if (playerStore.currentMusic !== null && playerStore.musicFile !== null) {
      playerStore.setListen(playerStore.currentMusic, playerStore.musicFile, playerStore.position);
      return;
    }
  };

  return {
    // Fonctions
    storeAdjacentMusicInQueue,
    loadQueueFile,
    playNextSong,
    playPreviousSong,
  };
}
