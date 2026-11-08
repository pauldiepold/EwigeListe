export type CreateRoundList = {
  id: number;
  name: string;
  closed: boolean;
};

export type CreateRoundProfile = {
  groupId: number;
  default: boolean;
};

export type CreateRoundPlayer = {
  id: number;
  fullName: string;
  avatarPath: string | null;
  groups: CreateRoundList[];
  profiles: CreateRoundProfile[];
};
