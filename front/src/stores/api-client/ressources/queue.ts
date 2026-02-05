import type { AddToQueue, GenerateRandomQueue, Queue as QueueType } from "@/utils/types";
import type { ApiClient } from "../model";

enum ApiRessourcePath {
  QUEUE = '/queue',
  RANDOM = '/queue/random',
}

export default class Queue {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(): Promise<QueueType> {
    return this.apiClient.get<QueueType>(ApiRessourcePath.QUEUE);
  }

  async add(data: AddToQueue): Promise<QueueType> {
    return this.apiClient.post<QueueType>(`${ApiRessourcePath.QUEUE}/add`, data, { Accept: 'application/ld+json' });
  }

  async reset(): Promise<boolean> {
    return this.apiClient.post<Response>(`${ApiRessourcePath.QUEUE}/reset`, {}, {}, true)
      .then(response => response.status === 204);
  }

  async generateRandom(data: GenerateRandomQueue): Promise<QueueType> {
    return this.apiClient.post<QueueType>(`${ApiRessourcePath.RANDOM}/generate`, data, { Accept: 'application/ld+json' });
  }

  async clearRandom(): Promise<boolean> {
    return this.apiClient.post<Response>(`${ApiRessourcePath.RANDOM}/clear`, {}, {}, true)
      .then(response => response.status === 204);
  }
}

