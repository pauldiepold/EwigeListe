import '@inertiajs/core';
import type { FlashToast } from './flash';

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    flashDataType: {
      toast?: FlashToast;
      /** Spieler aus der Schnellregistrierung, den `Rounds/Create` direkt auswählt. */
      addedPlayerId?: number;
    };
    /** Von `HandleInertiaRequests::share()` auf jeder Seite mitgeschickt. */
    sharedPageProps: {
      auth: {
        user: { id: number } | null;
        player: { id: number; surname: string; name: string } | null;
      };
      navigation: { showCurrentRound: boolean };
    };
  }
}
