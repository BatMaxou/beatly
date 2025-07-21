import type { PlaylistMusicUpdate, Playlist as PlaylistType } from "@/utils/types";
import type { ApiClient, CollectionResponse, DeleteResponse } from "../model";

interface ResourceResponse extends Partial<PlaylistType> {
  "@id": string;
}

enum ApiRessourcePath {
  PLAYLIST = "/playlists",
  PLATFORM = "/platform_playlists",
};

export default class Playlist {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(id: number | string): Promise<PlaylistType> {
    return this.apiClient.get<PlaylistType>(`${ApiRessourcePath.PLAYLIST}/${id}`, {
      Accept: "application/ld+json",
    });
  }

  async getAll(page: number = 1): Promise<CollectionResponse<PlaylistType>> {
    return await this.apiClient.get<CollectionResponse<PlaylistType>>(
      `${ApiRessourcePath.PLAYLIST}?page=${page}`,
      {
        Accept: "application/ld+json",
      },
    );
  }

  async create(data: Partial<PlaylistType>):   Promise<ResourceResponse> {
    return this.apiClient.post<ResourceResponse>(ApiRessourcePath.PLAYLIST, data, {
      Accept: "application/ld+json",
    });
  }

  async createPlatform(data: Partial<PlaylistType>): Promise<ResourceResponse> {
    return this.apiClient.post<ResourceResponse>(ApiRessourcePath.PLATFORM, data, {
      Accept: "application/ld+json",
    });
  }

  async update(id: number | string, data: Partial<PlaylistMusicUpdate>): Promise<ResourceResponse> {
    return this.apiClient.patch<ResourceResponse>(`${ApiRessourcePath.PLAYLIST}/${id}`, data, {
      Accept: "application/ld+json",
    });
  }

  async updateFiles(id: number | string, cover?: File, wallpaper?: File): Promise<PlaylistType> {
    const formData = new FormData();

    if (cover) {
      formData.append("cover", cover);
    }

    if (wallpaper) {
      formData.append("wallpaper", wallpaper);
    }

    return this.apiClient.post<PlaylistType>(`${ApiRessourcePath.PLAYLIST}/${id}/files`, formData, {
      Accept: "application/ld+json",
    });
  }

  async delete(id: number | string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath.PLAYLIST}/${id}`);
  }
}
