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

export enum RequestStatus {
  PENDING = "pending",
  ACCEPTED = "accepted",
  DECLINED = "declined",
}

export interface User {
  id: number;
  name: string;
  email: string;
  roles: Role[];
  avatar?: string;
  wallpaper?: string;
  artistRequest?: ArtistRequest;
}

export interface Artist extends User {
  "@id": string;
  "@type": string;
  musics?: Music[];
  featuredMusics?: Music[];
  albums?: Album[];
}

export type Album = {
  "@id": string;
  "@type": string;
  id: number;
  title: string;
  releaseDate: string;
  musics: Music[];
  cover?: string;
  wallpaper?: string;
  artists: Artist[];
  artist: Artist;
  isFavorite: boolean;
};

export type Category = {
  id: number;
  name: string;
  color: string;
};

export type MusicFile = {
  "@id": string;
  "@type": string;
  id: number;
  contentUrl: string;
  position?: number;
};

export type Music = {
  "@id": string;
  "@type": string;
  id: number;
  title: string;
  addedAt: string;
  album: Album;
  mainArtist: Artist;
  artists: Artist[];
  categories: Category[];
  file?: string;
  cover?: string;
  listeningsNumber?: number;
  duration?: number;
  albumPosition?: number;
  isFavorite: boolean;
};

export type PlaylistMusic = {
  "@id": string;
  id: number;
  music: Music;
  addedAt: string | null;
};

export type PlaylistMusicUpdate = {
  musics: (string | PlaylistMusic)[];
};

export type Playlist = {
  id: number;
  title: string;
  musics: PlaylistMusic[];
  cover?: string;
  wallpaper?: string;
  isFavorite: boolean;
  creator: User;
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

export type ArtistRequest = {
  message: string;
  files: string[];
  status: RequestStatus;
  user?: User;
};
