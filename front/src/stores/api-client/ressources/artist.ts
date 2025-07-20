import type { Artist as ArtistType } from "@/utils/types";
import type { ApiClient, CollectionResponse } from "../model";

const ApiRessourcePath = "/artists";

export default class Artist {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(id: number | string): Promise<ArtistType> {
    return this.apiClient.get<ArtistType>(`${ApiRessourcePath}/${id}`, {
      Accept: "application/ld+json",
    });
  }

  async getAll(): Promise<CollectionResponse<ArtistType>> {
    return this.apiClient.get<CollectionResponse<ArtistType>>(ApiRessourcePath, {
      Accept: "application/ld+json",
    });
  }
}
