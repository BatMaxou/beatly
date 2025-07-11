import Me from "./ressources/me";
import Music from "./ressources/music";
import Artist from "./ressources/artist";
import UserResource from "./ressources/user";
import Album from "./ressources/album";
import Playlist from "./ressources/playlist";
import Category from "./ressources/category";import Dashboard from "./ressources/dashboard";
import Listen from "./ressources/listen";
import { eraseCookie, getCookie, setCookie } from "@/utils/cookies";
import { apiBaseUrl } from "@/utils/tools";
import type { User } from "@/utils/types";

export interface LoginResponse {
  token: string;
  user?: User;
}

export interface DeleteResponse {
  success: boolean;
}

export class ApiClient {
  baseUrl: string;
  me: Me;
  music: Music;
  user: UserResource;
  category: Category;
  album: Album;
  playlist: Playlist;
  artist: Artist;
  dashboard: Dashboard;
  listen: Listen;
  token: string | null;

  constructor() {
    this.baseUrl = apiBaseUrl;
    this.me = new Me(this);
    this.music = new Music(this);
    this.user = new UserResource(this);
    this.category = new Category(this);
    this.album = new Album(this);
    this.playlist = new Playlist(this);
    this.artist = new Artist(this);
    this.dashboard = new Dashboard(this);
    this.listen = new Listen(this);
    this.token = getCookie("token");
  }

  async get<T>(url: string, additionnalHeaders: HeadersInit = {}): Promise<T> {
    return fetch(`${this.baseUrl}${url}`, {
      headers: {
        Accept: "application/json",
        ...additionnalHeaders,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    }).then((response) => response.json());
  }

  async getStream(url: string): Promise<ReadableStream> {
    return fetch(
      `${this.baseUrl}${url}`,
      this.token ? { headers: { Authorization: `Bearer ${this.token}` } } : undefined,
    )
      .then((response) => response.body)
      .then((body) => {
        if (!body) {
          throw new Error("Failed to fetch stream");
        }
        return body;
      });
  }

  async post<T>(url: string, body: object = {}, additionnalHeaders: HeadersInit = {}): Promise<T> {
    const isFormData = body instanceof FormData;

    const headers: HeadersInit = isFormData
      ? {
          Accept: "application/json",
          ...additionnalHeaders,
        }
      : {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...additionnalHeaders,
        };

    return fetch(`${this.baseUrl}${url}`, {
      method: "POST",
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: isFormData ? body : JSON.stringify(body),
    }).then((response) => response.json());
  }

  async patch<T>(url: string, body: object, additionnalHeaders: HeadersInit = {}): Promise<T> {
    const headers: HeadersInit = {
      Accept: "application/json",
      "Content-Type": "application/merge-patch+json",
      ...additionnalHeaders,
    };

    return fetch(`${this.baseUrl}${url}`, {
      method: "PATCH",
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: JSON.stringify(body),
    }).then((response) => response.json());
  }

  async delete(url: string): Promise<DeleteResponse> {
    return fetch(`${this.baseUrl}${url}`, {
      method: "DELETE",
      headers: {
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    }).then((response) => ({ success: response.status === 204 }));
  }

  async login(email: string, password: string): Promise<object> {
    return this.post<LoginResponse>("/login", { username: email, password })
      .then((response) => {
        if (response.token) {
          const decodedTokenExp: number = JSON.parse(atob(response.token.split(".")[1]))?.exp ?? 0;
          setCookie("token", response.token, new Date(decodedTokenExp * 1000));
          this.token = response.token;
        }

        return response;
      })
      .then(async (response) => {
        if (response.token) {
          return {
            ...response,
            user: await this.me.get().then((user) => user),
          };
        }

        return response;
      });
  }

  logout(): void {
    this.token = null;
    eraseCookie("token");
  }
}
