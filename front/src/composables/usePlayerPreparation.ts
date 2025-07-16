import { useApiClient } from "@/stores/api-client";
import { usePlayerStore } from "@/stores/player";
import { streamToAudioUrl } from "@/utils/stream";
import type { AddToQueue, Music, Queue, QueueItem } from "@/utils/types";

export function usePlayerPreparation() {
  const playerStore = usePlayerStore();
  const { apiClient } = useApiClient();

  /**
   * Trouve la dernière musique de la queue
   */
  const getLastQueueItem = (items: QueueItem[]) => {
    return items.reduce((max, item) => (item.position > max ? item.position : max), 0);
  };

  /**
   * Lancement initial de la musique.
   * Execution de storeAdjacentMusicInQueue pour définir les titres précédents et suivants.
   *
   */
  const loadQueue = async (
    origin: string,
    parentId: string,
    position: number,
    musics?: { music: Music }[] | null,
  ) => {
    if (playerStore.queue) {
      await playerStore.clearQueue();
    }
    let queue: Queue | null = null;
    if (origin === "album") {
      queue = await apiClient.queue.add({ album: parentId, currentPosition: position });
    } else if (origin === "playlist") {
      queue = await apiClient.queue.add({ playlist: parentId, currentPosition: position });
    } else if (origin === "top-titles" && musics) {
      const musicIds = musics
        .map((music) => music.music["@id"])
        .filter((id) => id !== undefined && id !== null);
      queue = await apiClient.queue.add({ musics: musicIds, currentPosition: position });
    }

    if (queue) {
      playerStore.setQueue(queue, parentId);
    }
  };

  /**
   * Ajout d'un album / titre / playlist / liste de titres à la file d'attente
   * Relance loadQueueFile pour charger les fichiers audio si ils n'existent pas dans la liste
   */
  const addToQueue = async (data: AddToQueue) => {
    const { music, playlist, album, shouldBeNext, currentPosition } = data;
    let queueUpdated;
    const origin = String(music || playlist || album);
    if (music) {
      queueUpdated = await apiClient.queue.add({
        music: music,
        shouldBeNext: shouldBeNext,
        currentPosition: shouldBeNext ? currentPosition : undefined,
      });
      // } else if (musics && musics.length > 0) {
      //   queueUpdated = await apiClient.queue.add({
      //     musics: musics,
      //     shouldBeNext: shouldBeNext,
      //     currentPosition: shouldBeNext ? currentPosition : undefined,
      //   });
    } else if (playlist) {
      queueUpdated = await apiClient.queue.add({
        playlist,
        shouldBeNext: shouldBeNext,
        currentPosition: shouldBeNext ? currentPosition : undefined,
      });
    } else if (album) {
      queueUpdated = await apiClient.queue.add({
        album,
        shouldBeNext: shouldBeNext,
        currentPosition: shouldBeNext ? currentPosition : undefined,
      });
    }
    if (queueUpdated) {
      const wasQueueEmpty = playerStore.queue === null && playerStore.queueParent === null;
      playerStore.setQueue(
        queueUpdated,
        wasQueueEmpty ? origin : playerStore.queueParent || origin,
      );
      // Si je suis dans une randomQueue,
      // Je récupère lastQueue et queueUpdated et je récupère les éléments de différence dans l'ordre
      // Si shouldBeNext, ajouter la liste de musiques à la randomQueue en fonction de currentPosition
      // Sinon, ajout a la fin du tableau randomQueue

      // Problème a cause des positions, a voir comment on va gérer le cas random
      storeAdjacentMusicInQueue();
    }
  };

  /**
   * Génération d'une randomQueue à partir de la queue actuelle
   * si elle est présente
   */
  const loadRandomQueue = async (position?: number) => {
    if (playerStore.queue) {
      if (playerStore.randomQueue) {
        await playerStore.clearRandomQueue();
      }
      const randomQueue = await apiClient.queue.generateRandom({
        currentPosition: position !== undefined ? position : playerStore.position,
      });
      if (randomQueue) {
        playerStore.setRandomQueue(randomQueue);
        playerStore.setPosition(1);
        storeAdjacentMusicInQueue();
        return randomQueue;
      }
    }
    return null;
  };

  /**
   * Définit les titres précédents et suivants dans le store du player
   * en fonction de la queue ou randomQueue
   */
  const storeAdjacentMusicInQueue = async () => {
    let previousMusic: Music | null = null;
    let nextMusic = null;
    if (!playerStore.queue) return;

    const queueItems = Object.values(
      playerStore.isRandomQueue && playerStore.randomQueue
        ? playerStore.randomQueue.queueItems
        : playerStore.queue.queueItems,
    );

    if (queueItems.length === 0) return;

    let previousInQueue = queueItems.find((item) => item.position === playerStore.position - 1);

    if (!previousInQueue && playerStore.isRepeatQueue) {
      const lastPosition = getLastQueueItem(queueItems);
      previousInQueue = queueItems.find((item) => item.position === lastPosition);
    }

    let nextInQueue = queueItems.find((item) => item.position === playerStore.position + 1);

    if (!nextInQueue && playerStore.isRepeatQueue) {
      nextInQueue = queueItems.find((item) => item.position === 1);
    }

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
  const playNextSong = async () => {
    if (playerStore.nextMusic !== null && playerStore.nextMusicFile !== null) {
      const queueItems = Object.values(
        playerStore.isRandomQueue && playerStore.randomQueue
          ? playerStore.randomQueue.queueItems
          : playerStore.queue?.queueItems || {},
      );

      const isBackToStart =
        playerStore.isRepeatQueue &&
        queueItems.length > 0 &&
        playerStore.nextMusic.id === queueItems.find((item) => item.position === 1)?.music.id;

      const newPosition = isBackToStart ? 1 : playerStore.position + 1;

      await playerStore.setListen(playerStore.nextMusic, playerStore.nextMusicFile, newPosition);
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
  const playPreviousSong = async () => {
    if (playerStore.previousMusic !== null && playerStore.previousMusicFile !== null) {
      const queueItems = Object.values(
        playerStore.isRandomQueue && playerStore.randomQueue
          ? playerStore.randomQueue.queueItems
          : playerStore.queue?.queueItems || {},
      );

      const isBackToEnd =
        playerStore.isRepeatQueue &&
        queueItems.length > 0 &&
        playerStore.previousMusic.id ===
          queueItems.find((item) => item.position === getLastQueueItem(queueItems))?.music.id;

      const newPosition = isBackToEnd ? getLastQueueItem(queueItems) : playerStore.position - 1;

      await playerStore.setListen(
        playerStore.previousMusic,
        playerStore.previousMusicFile,
        newPosition,
      );
      storeAdjacentMusicInQueue();
    } else if (playerStore.currentMusic !== null && playerStore.musicFile !== null) {
      await playerStore.setListen(
        playerStore.currentMusic,
        playerStore.musicFile,
        playerStore.position,
      );
      return;
    }
  };

  return {
    // Fonctions
    loadQueue,
    loadRandomQueue,
    addToQueue,
    storeAdjacentMusicInQueue,
    loadQueueFile,
    playNextSong,
    playPreviousSong,
  };
}
