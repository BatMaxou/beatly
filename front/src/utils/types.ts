export enum Role {
  PLATFORM = "ROLE_PLATFORM",
  ARTIST = "ROLE_ARTIST",
  USER = "ROLE_USER",
  BANNED = "ROLE_BANNED",
}

export enum PlaylistType {
  PRIVATE = "Playlist",
  PLATFORM = "PlatformPlaylist",
}

export interface User {
  id: number;
  name: string;
  email: string;
  roles: Role[];
  avatar?: string;
  wallpaper?: string;
}

export interface Artist extends User {
  "@id": string;
  musics?: Music[];
  featuredMusics?: Music[];
  albums?: Album[];
}

export type AlbumMusic = {
  id: number;
  music: Music;
  position: number;
  addedAt: string;
};

export type Album = {
  "@id": string;
  id: number;
  title: string;
  releaseDate: string;
  musics: AlbumMusic[];
  cover?: string;
  wallpaper?: string;
  artists: Artist[];
};

export type Category = {
  id: number;
  name: string;
  color: string;
};

export type MusicFile = {
  id: number;
  contentUrl: string;
  position?: number;
};

export type Music = {
  "@id": string;
  id: number;
  title: string;
  mainArtist: Artist;
  artists: Artist[];
  categories: Category[];
  file?: string;
  cover?: string;
  listeningsNumber?: number;
  duration?: number;
};

export type PlaylistMusic = {
  id: number;
  music: Music;
  addedAt: string | null;
};

export type Playlist = {
  id: number;
  title: string;
  musics: PlaylistMusic[];
  cover?: string;
  wallpaper?: string;
  "@type"?: PlaylistType;
  "@id": string;
};

export type Listen = {
  music: string;
  playlist?: string;
  album?: string;
};

export type LastListened<T> = {
  target: T;
  listenedAt: string;
};

export type Dashboard = {
  lastListened: LastListened<Music | Album | Playlist>[];
  mostPopularCategories: Category[];
  mostLikedMusics: Music[];
  mostListenedMusics: Music[];
  mostLikedPlaylists: Playlist[];
};

export type Recommendation = {
  recommendations: Music[];
};

export type QueueItem = {
  music: Music;
  position: number;
};

export type Queue = {
  queueItems: QueueItem[];
};

export type AddToQueue = {
  music?: string;
  musics?: string[];
  playlist?: string;
  album?: string;
  shouldBeNext?: boolean;
  currentPosition?: number;
};

export type GenerateRandomQueue = {
  currentPosition: number;
};

export type SearchResult = {
  results: Array<Music | Playlist | Album | Artist>;
};

export type FavoriteAction = {
  music?: string;
  playlist?: string;
  album?: string;
};

export type Favorite<T> = {
  target: T;
  addedAt: string;
};

export type Favorites = {
  musics?: Favorite<Music>[];
  playlists?: Favorite<Playlist>[];
  albums?: Favorite<Album>[];
};
