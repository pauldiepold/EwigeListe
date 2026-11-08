/** Einmalige Meldung nach einem Redirect, gesetzt per `App\Http\Toast`. */
export type FlashToast = {
  type: 'success' | 'error';
  message: string;
};
