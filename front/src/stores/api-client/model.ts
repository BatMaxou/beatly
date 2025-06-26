import Me from "./ressources/me";
import { eraseCookie, getCookie, setCookie } from "@/utils/cookies";
import { apiBaseUrl } from "@/utils/tools";
import type { User } from "@/utils/types";

export interface LoginResponse {
  token: string;
  user?: User;
}

export class ApiClient {
  baseUrl: string;
  me: Me
  token: string | null;

  constructor() {;
    this.baseUrl = apiBaseUrl;
    this.me = new Me(this);
    this.token = getCookie('token')
  }

  async get<T>(url: string): Promise<T> {
    return fetch(`${this.baseUrl}${url}`, this.token ? { headers: { Authorization: `Bearer ${this.token}` } } : undefined)
      .then(response => response.json())
  }

  async post<T>(url: string, body: object, additionnalHeaders: HeadersInit = {}): Promise<T> {
    const headers: HeadersInit = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...additionnalHeaders,
    }

    return fetch(`${this.baseUrl}${url}`, {
      method: 'POST',
      headers: {
        ...headers,
        ...( this.token ? { Authorization: this.token } : {} )
      },
      body: JSON.stringify(body)
    })
      .then(response => response.json())
  }

  async put<T>(url: string, body: object, additionnalHeaders: HeadersInit = {}): Promise<T> {
    const headers: HeadersInit = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...additionnalHeaders,
    }

    return fetch(`${this.baseUrl}${url}`, {
      method: 'PUT',
      headers: {
        ...headers,
        ...( this.token ? { Authorization: this.token } : {} )
      },
      body: JSON.stringify(body)
    })
      .then(response => response.json())
  }

  async delete<T>(url: string): Promise<T> {
    return fetch(`${this.baseUrl}${url}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
      },
    })
      .then(response => response.json())
  }

  async login(email: string, password: string): Promise<object> {
    return this.post<LoginResponse>('/login', { username: email, password })
      .then(response => {
        if (response.token) {
          const decodedTokenExp: number = JSON.parse(atob(response.token.split('.')[1]))?.exp ?? 0;
          setCookie('token', response.token, new Date(decodedTokenExp * 1000))
          this.token = response.token
        }

        return response
      })
      .then(async (response) => {
        if (response.token) {
          return {
            ...response,
            user: await this.me.get().then(user => user)
          }
        }

        return response
      })
  }

  logout(): void {
    this.token = null;
    eraseCookie('token');
  }
}
