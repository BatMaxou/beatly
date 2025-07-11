import type { Artist as ArtistType } from "@/utils/types";
import type { ApiClient } from "../model";

const ApiRessourcePath = '/artists';

export default class User {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(id: number|string): Promise<ArtistType> {
    return this.apiClient.get<ArtistType>(`${ApiRessourcePath}/${id}`, { Accept: 'application/ld+json' });
  }

  async getAll(): Promise<ArtistType[]> {
    return this.apiClient.get<ArtistType[]>(ApiRessourcePath);
  }
}
