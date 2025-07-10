import type { Playlist as PlaylistType } from "@/utils/types";
import type { ApiClient, DeleteResponse } from "../model";

interface ResourceResponse extends Partial<PlaylistType> {
  '@id': string;
}

const ApiRessourcePath = '/playlists';

export default class Playlist {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(id: number|string): Promise<PlaylistType> {
    return this.apiClient.get<PlaylistType>(`${ApiRessourcePath}/${id}`, { Accept: 'application/ld+json' });
  }

  async getAll(): Promise<PlaylistType[]> {
    return this.apiClient.get<PlaylistType[]>(ApiRessourcePath);
  }

  async create(data: Partial<PlaylistType>): Promise<ResourceResponse> {
    return this.apiClient.post<ResourceResponse>(ApiRessourcePath, data, { Accept: 'application/ld+json' });
  }

  async update(id: number|string, data: Partial<PlaylistType>): Promise<ResourceResponse> {
    return this.apiClient.patch<ResourceResponse>(`${ApiRessourcePath}/${id}`, data, { Accept: 'application/ld+json' });
  }

  async delete(id: number|string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath}/${id}`)
  }
}
