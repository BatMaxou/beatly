import type { SearchResult } from "@/utils/types";
import type { ApiClient } from "../model";

export default class Search {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async search(query: string): Promise<SearchResult> {
    return this.apiClient.get<SearchResult>(`/search?query=${encodeURIComponent(query)}`, { Accept: "application/ld+json" });
  }
}
