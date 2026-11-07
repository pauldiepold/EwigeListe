export type EditUser = {
  id: number;
  player_id: number;
  email: string;
  surname: string;
  name: string;
  avatar_path: string | null;
};

export type EditUserProfile = {
  id: number;
  name: string;
  closed: boolean;
  default: boolean;
  selectable: boolean;
};
