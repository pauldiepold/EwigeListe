export type EditUser = {
  id: number;
  playerId: number;
  email: string;
  firstName: string;
  lastName: string;
  avatarPath: string | null;
};

export type EditUserProfile = {
  id: number;
  name: string;
  closed: boolean;
  default: boolean;
  selectable: boolean;
};
