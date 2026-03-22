declare global {
  interface Window {
    grecaptcha: {
      render: (
        container: HTMLElement,
        params: {
          sitekey: string;
          callback: (token: string) => void;
          'expired-callback': () => void;
        },
      ) => number;
      reset: (widgetId: number) => void;
      ready: (callback: () => void) => void;
    };
  }
}

export type SocialiteUserData = {
  id: number;
  email: string;
  provider: string;
  surname: string;
  name: string;
};

export type ResetPasswordProps = {
  token: string;
  email: string;
};

export type RegisterProps = {
  captchaSiteKey: string;
};

export type RegisterOrAttachProps = {
  socialiteUser: SocialiteUserData;
  existingUserEmail: string | null;
};
