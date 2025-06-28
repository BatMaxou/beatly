import type { ApiClient, DeleteResponse } from "../model";

export interface UploadResponse {
  '@id': string;
  id: number;
  contentUrl: string;
}

enum ApiRessourcePath {
  MUSIC = '/music',
  FILE = '/music_files',
}

export default class Music {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async getFile(id: number|string): Promise<ReadableStream> {
    return this.apiClient.getStream(`${ApiRessourcePath.FILE}/${id}`);
  }

  async upload(file: File): Promise<UploadResponse> {
    const formData = new FormData();
    formData.append("file", file);

    return this.apiClient.post<UploadResponse>(`${ApiRessourcePath.FILE}`, formData, { Accept: 'application/ld+json' })
      .then(response => {
        if (!response.id) {
          throw new Error('Failed to upload music file');
        }

        return response;
      })
  }

  async updateFile(id: number|string, file: File): Promise<UploadResponse> {
    this.deleteFile(id)

    const formData = new FormData();
    formData.append("file", file);

    return this.upload(file)
  }

  async deleteFile(id: number|string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath.FILE}/${id}`)
  }
}
