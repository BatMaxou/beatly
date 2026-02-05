import type { Playlist, User } from "@/utils/types";
import type { ApiClient, CollectionResponse } from "../model";

const ApiRessourcePath = "/me";

export default class Me {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(): Promise<User> {
    return this.apiClient.get<User>(`${ApiRessourcePath}`)
  }

  async getPlaylists(): Promise<CollectionResponse<Playlist>> {
    return this.apiClient.get<CollectionResponse<Playlist>>(`${ApiRessourcePath}/playlists`, { Accept: "application/ld+json" });
  }
}
