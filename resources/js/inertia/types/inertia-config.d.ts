import '@inertiajs/core';
import type { FlashToast } from './flash';

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    flashDataType: {
      toast?: FlashToast;
      /** Spieler aus der Schnellregistrierung, den `Rounds/Create` direkt auswählt. */
      addedPlayerId?: number;
    };
  }
}
