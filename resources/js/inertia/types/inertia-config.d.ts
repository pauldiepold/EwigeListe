import '@inertiajs/core';
import type { FlashToast } from './flash';

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    flashDataType: {
      toast?: FlashToast;
    };
  }
}
