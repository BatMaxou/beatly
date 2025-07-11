import type { Dashboard as DashboardType, Recommendation as RecommendationType } from "@/utils/types";
import type { ApiClient } from "../model";

enum ApiRessourcePath {
  COMMON = '/dashboard',
  RECOMMENDATION = '/dashboard/recommendations',
}

export default class Dashboard {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(): Promise<DashboardType> {
    return this.apiClient.get<DashboardType>(`${ApiRessourcePath.COMMON}`);
  }

  async getRecommendations(): Promise<RecommendationType[]> {
    return this.apiClient.get<RecommendationType[]>(`${ApiRessourcePath.RECOMMENDATION}`);
  }
}
