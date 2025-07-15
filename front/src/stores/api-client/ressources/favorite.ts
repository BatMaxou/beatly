import type { Favorites, FavoriteAction } from "@/utils/types";
import type { ApiClient } from "../model";

const ApiRessourcePath = '/favorites';

export default class Favorite {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async add(data: FavoriteAction): Promise<boolean> {
    return this.apiClient.post<Response>(`${ApiRessourcePath}/add`, data, {}, true)
      .then(response => response.status === 201)
  }

  async remove(data: FavoriteAction): Promise<boolean> {
    return this.apiClient.post<Response>(`${ApiRessourcePath}/remove`, data, {}, true)
      .then(response => response.status === 204)
  }

  async getAll(): Promise<Favorites> {
    return this.apiClient.get<Favorites>(ApiRessourcePath, { Accept: 'application/ld+json' });
  }

  async getMusics(): Promise<Favorites> {
    return this.apiClient.get<Favorites>(`${ApiRessourcePath}/musics`, { Accept: 'application/ld+json' });
  }

  async getAlbums(): Promise<Favorites> {
    return this.apiClient.get<Favorites>(`${ApiRessourcePath}/albums`, { Accept: 'application/ld+json' });
  }

  async getPlaylists(): Promise<Favorites> {
    return this.apiClient.get<Favorites>(`${ApiRessourcePath}/playlists`, { Accept: 'application/ld+json' });
  }
}
