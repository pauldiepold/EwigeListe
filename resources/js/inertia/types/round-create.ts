export type CreateRoundGroup = {
  id: number;
  name: string;
  closed: number;
};

export type CreateRoundProfile = {
  group_id: number;
  default: boolean;
};

export type CreateRoundPlayer = {
  id: number;
  surname: string;
  name: string;
  avatar_path: string;
  groups: CreateRoundGroup[];
  profiles: CreateRoundProfile[];
};
