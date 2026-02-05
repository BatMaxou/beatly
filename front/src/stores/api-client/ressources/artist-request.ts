import type { ArtistRequest as ArtistRequestType } from "@/utils/types";
import type { ApiClient, CollectionResponse } from "../model";

interface ResourceResponse extends Partial<ArtistRequestType> {
  "@id": string;
}

interface ArtistRequestActionResponse {
  result: boolean;
}

const ApiRessourcePath = "/artist_requests";

export default class ArtistRequest {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async create(data: Partial<ArtistRequestType>): Promise<ResourceResponse> {
    return this.apiClient.post<ResourceResponse>(`${ApiRessourcePath}`, data, {
      Accept: "application/ld+json",
    });
  }

  async accept(id: string): Promise<boolean> {
    return this.apiClient
      .post<ArtistRequestActionResponse>(`${ApiRessourcePath}/accept`, {
        artistRequest: id,
      })
      .then((response) => response.result);
  }

  async decline(id: string): Promise<boolean> {
    return this.apiClient
      .post<ArtistRequestActionResponse>(`${ApiRessourcePath}/decline`, {
        artistRequest: id,
      })
      .then((response) => response.result);
  }

  async get(id: number | string): Promise<ArtistRequestType> {
    return this.apiClient.get<ArtistRequestType>(`${ApiRessourcePath}/${id}`, {
      Accept: "application/ld+json",
    });
  }

  async getAll(): Promise<CollectionResponse<ArtistRequestType>> {
    return await this.apiClient.get<CollectionResponse<ArtistRequestType>>(ApiRessourcePath, {
      Accept: "application/ld+json",
    });
  }
}
