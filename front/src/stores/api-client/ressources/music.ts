import type { ApiClient, CollectionResponse, DeleteResponse } from "../model";
import type { Music as MusicType } from "@/utils/types";

export interface UploadResponse {
  "@id": string;
  id: number;
  contentUrl: string;
}

interface ResourceResponse extends Partial<MusicType> {
  "@id": string;
}

enum ApiRessourcePath {
  MUSIC = "/music",
  FILE = "/music_files",
}

export default class Music {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async getAll(page: number = 1): Promise<CollectionResponse<MusicType>> {
    return await this.apiClient.get<CollectionResponse<MusicType>>(
      `${ApiRessourcePath.MUSIC}?page=${page}`,
      {
        Accept: "application/ld+json",
      },
    );
  }

  async createMusic(data: FormData, file: File): Promise<ResourceResponse> {
    const response = await this.upload(file);

    data.append("file", response["@id"]);

    return this.apiClient.post<ResourceResponse>(`${ApiRessourcePath.MUSIC}`, data, {
      Accept: "application/ld+json",
    });
  }

  async updateMusic(id: number | string, data: Partial<MusicType>): Promise<ResourceResponse> {
    return this.apiClient.patch<ResourceResponse>(`${ApiRessourcePath.MUSIC}/${id}`, data, {
      Accept: "application/ld+json",
    });
  }

  async getFile(id: number | string): Promise<ReadableStream> {
    return this.apiClient.getStream(`${ApiRessourcePath.FILE}/${id}`);
  }

  async upload(file: File): Promise<UploadResponse> {
    const formData = new FormData();
    formData.append("file", file);

    return this.apiClient
      .post<UploadResponse>(`${ApiRessourcePath.FILE}`, formData, { Accept: "application/ld+json" })
      .then((response) => {
        if (!response.id) {
          throw new Error("Failed to upload music file");
        }

        return response;
      });
  }

  async updateFile(
    id: number | string,
    file: File,
    musicId: number | string | null = null,
  ): Promise<UploadResponse> {
    const formData = new FormData();
    formData.append("file", file);

    const response = await this.upload(file);

    if (musicId) {
      await this.updateMusic(musicId, { file: response["@id"] });
    }

    await this.deleteFile(id);

    return response;
  }

  async updateCover(id: number | string, file: File): Promise<MusicType> {
    const formData = new FormData();
    formData.append("cover", file);

    return this.apiClient.post<MusicType>(`${ApiRessourcePath.MUSIC}/${id}/files`, formData, {
      Accept: "application/ld+json",
    });
  }

  async deleteFile(id: number | string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath.FILE}/${id}`);
  }
}
