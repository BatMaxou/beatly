import type { ApiClient } from "../model";

export default class Music {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async getFile(id: number|string): Promise<ReadableStream> {
    return this.apiClient.getStream(`/music_files/${id}`);
  }
}
