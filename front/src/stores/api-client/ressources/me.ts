import type { User } from "@/utils/types";
import type { ApiClient } from "../model";

export default class Me {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get() {
    return this.apiClient.get<User>(`/me`);
  }
}
