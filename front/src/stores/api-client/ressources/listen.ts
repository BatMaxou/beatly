import type { Listen as ListenType } from "@/utils/types";
import type { ApiClient } from "../model";

export default class Listen {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async play(data: ListenType): Promise<boolean> {
    return this.apiClient.post<boolean>('/listen', data)
      .then(() => true)
      .catch(() => false);
  }
}
