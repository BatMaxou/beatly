import type { AddToQueue, Queue as QueueType } from "@/utils/types";
import type { ApiClient } from "../model";

const ApiRessourcePath = '/queue';

export default class Queue {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(): Promise<QueueType> {
    return this.apiClient.get<QueueType>(ApiRessourcePath);
  }

  async add(data: AddToQueue): Promise<QueueType> {
    return this.apiClient.post<QueueType>(`${ApiRessourcePath}/add`, data);
  }

  async reset(): Promise<boolean> {
    return this.apiClient.post<Response>(`${ApiRessourcePath}/reset`, {})
      .then(response => response.status === 204);
  }
}

