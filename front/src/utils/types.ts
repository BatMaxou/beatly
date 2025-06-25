
export enum Role {
  PLATFORM = 'ROLE_PLATFORM',
  ARTIST = 'ROLE_ARTIST',
  USER = 'ROLE_USER',
  BANNED = 'ROLE_BANNED',
}

export type User = {
  name: string;
  email: string;
  roles: Role[];
};

export type MusicFile = {
  id: number;
  contentUrl: string;
};
