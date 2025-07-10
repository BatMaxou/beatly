import type { Album as AlbumType } from "@/utils/types";
import type { ApiClient, DeleteResponse } from "../model";

interface ResourceResponse extends Partial<AlbumType> {
  '@id': string;
}

const ApiRessourcePath = '/albums';

export default class Album {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(id: number|string): Promise<AlbumType> {
    return this.apiClient.get<AlbumType>(`${ApiRessourcePath}/${id}`);
  }

  async getAll(): Promise<AlbumType[]> {
    return this.apiClient.get<AlbumType[]>(ApiRessourcePath);
  }

  async create(data: Partial<AlbumType>): Promise<ResourceResponse> {
    return this.apiClient.post<ResourceResponse>(ApiRessourcePath, data, { Accept: 'application/ld+json' });
  }

  async update(id: number|string, data: Partial<AlbumType>): Promise<ResourceResponse> {
    return this.apiClient.patch<ResourceResponse>(`${ApiRessourcePath}/${id}`, data, { Accept: 'application/ld+json' });
  }

  async updateFiles(id: number|string, cover?: File, wallpaper?: File): Promise<AlbumType> {
    const formData = new FormData();

    if (cover) {
      formData.append("cover", cover);
    }

    if (wallpaper) {
      formData.append("wallpaper", wallpaper);
    }

    return this.apiClient.post<AlbumType>(`${ApiRessourcePath}/${id}/files`, formData, { Accept: 'application/ld+json' });
  }

  async delete(id: number|string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath}/${id}`)
  }
}
