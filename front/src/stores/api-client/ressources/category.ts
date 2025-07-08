import type { Category as CategoryType } from "@/utils/types";
import type { ApiClient, DeleteResponse } from "../model";

interface ResourceResponse extends Partial<CategoryType> {
  '@id': string;
}

const ApiRessourcePath = '/categories';

export default class Category {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async get(id: number|string): Promise<CategoryType> {
    return this.apiClient.get<CategoryType>(`${ApiRessourcePath}/${id}`);
  }

  async getAll(): Promise<CategoryType[]> {
    return this.apiClient.get<CategoryType[]>(ApiRessourcePath);
  }

  async create(data: Partial<CategoryType>): Promise<ResourceResponse> {
    return this.apiClient.post<ResourceResponse>(ApiRessourcePath, data, { Accept: 'application/ld+json' });
  }

  async update(id: number|string, data: Partial<CategoryType>): Promise<ResourceResponse> {
    return this.apiClient.patch<ResourceResponse>(`${ApiRessourcePath}/${id}`, data, { Accept: 'application/ld+json' });
  }

  async delete(id: number|string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath}/${id}`)
  }
}
