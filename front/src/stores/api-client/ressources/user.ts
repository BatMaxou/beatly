import type { User as UserType } from "@/utils/types";
import type { ApiClient, CollectionResponse, DeleteResponse } from "../model";

export type RegisterData = {
  name: string;
  password: string;
  email: string;
};

type RegisterResponse = {
  result: boolean;
};

export type ForgotPasswordData = {
  email: string;
};

type ForgotPasswordResponse = {
  result: boolean;
};

export type ResetPasswordData = {
  token: string;
  password: string;
};

type ResetPasswordResponse = {
  result: boolean;
  message: string;
};

export type VerifyTokenData = {
  token: string;
};

type VerifyTokenResponse = {
  result: boolean;
};

interface ResourceResponse extends Partial<UserType> {
  "@id": string;
}

const ApiRessourcePath = "/users";

export default class User {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async register(data: RegisterData): Promise<RegisterResponse> {
    return this.apiClient.post<{ result: boolean }>(`/register`, data).then((response) => ({
      result: response.result,
    }));
  }

  async forgotPassword(data: ForgotPasswordData): Promise<ForgotPasswordResponse> {
    const response = await this.apiClient
      .post<{ response: string; result: boolean }>(`/forgot-password`, data)
      .then((response) => ({
        result: response.result,
      }));
    return response;
  }

  async resetPassword(data: ResetPasswordData): Promise<ResetPasswordResponse> {
    const response = await this.apiClient
      .post<{ message: string; result: boolean }>(`/reset-password`, data)
      .then((response) => ({
        message: response.message,
        result: response.result,
      }));
    return response;
  }

  async verifyToken(data: VerifyTokenData): Promise<VerifyTokenResponse> {
    try {
      const response = await this.apiClient
        .post<{ result: boolean }>(`/verify-token`, data)
        .then((response) => ({
          result: response.result,
        }));
      return response;
    } catch (error) {
      console.error("Erreur lors de la vérification du token:", error);
      return { result: false };
    }
  }

  async get(id: number | string): Promise<UserType> {
    return this.apiClient.get<UserType>(`${ApiRessourcePath}/${id}`, {
      Accept: "application/ld+json",
    });
  }

  async getAll(): Promise<CollectionResponse<UserType>> {
    return this.apiClient.get<CollectionResponse<UserType>>(ApiRessourcePath, {
      Accept: "application/ld+json",
    });
  }

  async update(id: number | string, data: Partial<UserType>): Promise<ResourceResponse> {
    return this.apiClient.patch<ResourceResponse>(`${ApiRessourcePath}/${id}`, data, {
      Accept: "application/ld+json",
    });
  }

  async updateFiles(id: number | string, avatar?: File, wallpaper?: File): Promise<UserType> {
    const formData = new FormData();

    if (avatar) {
      formData.append("avatar", avatar);
    }

    if (wallpaper) {
      formData.append("wallpaper", wallpaper);
    }

    return this.apiClient.post<UserType>(`${ApiRessourcePath}/${id}/files`, formData, {
      Accept: "application/ld+json",
    });
  }

  async delete(id: number | string): Promise<DeleteResponse> {
    return this.apiClient.delete(`${ApiRessourcePath}/${id}`);
  }
}
