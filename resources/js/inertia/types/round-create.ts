export type CreateRoundList = {
  id: number;
  name: string;
  closed: boolean;
};

export type CreateRoundProfile = {
  group_id: number;
  default: boolean;
};

export type CreateRoundPlayer = {
  id: number;
  surname: string;
  name: string;
  avatar_path: string | null;
  groups: CreateRoundList[];
  profiles: CreateRoundProfile[];
};
