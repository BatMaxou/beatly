import type { LastListened as LastListenedType, Playlist, Music, Album } from "@/utils/types";
import type { ApiClient, CollectionResponse } from "../model";

type LastListenedResource = LastListenedType<Music | Playlist | Album>;

const ApiRessourcePath = "/last_listened";

export default class LastListened {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async getAll(): Promise<CollectionResponse<LastListenedResource>> {
    return this.apiClient.get<CollectionResponse<LastListenedResource>>(ApiRessourcePath, {
      Accept: "application/ld+json",
    });
  }
}
