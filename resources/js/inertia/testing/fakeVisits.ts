import { router } from '@inertiajs/vue3';
import type { Errors, VisitOptions } from '@inertiajs/core';
import { vi } from 'vitest';

/** Die Page, die `onSuccess` bekommt, typisiert mit den geteilten Props der App. */
type SuccessPage = Parameters<NonNullable<VisitOptions['onSuccess']>>[0];

type FakeVisitResult = { errors: Errors } | { page?: Partial<SuccessPage> };

/**
 * Ersetzt nur den Versand eines Inertia-Requests: `useForm` bleibt echt, der Spy spielt die
 * Callbacks in der Reihenfolge des Routers durch (`onBefore`, `onStart`, dann `onSuccess` oder
 * `onError`, zuletzt `onFinish`). Ohne `errors` gilt der Request als erfolgreich; `page` landet
 * in `onSuccess` (z. B. mit `flash`). Der Spy zeigt, ob und wohin gesendet wurde:
 * `expect(post).toHaveBeenCalledWith(url, data, expect.anything())`.
 */
export function fakeVisits(
  method: 'post' | 'put' | 'patch',
  result: FakeVisitResult = { page: {} },
) {
  return vi
    .spyOn(router, method)
    .mockImplementation(async (_url, _data, options: VisitOptions = {}) => {
      const visit = {} as never;
      options.onBefore?.(visit);
      options.onStart?.(visit);
      if ('errors' in result) {
        await options.onError?.(result.errors);
      } else {
        await options.onSuccess?.((result.page ?? {}) as SuccessPage);
      }
      options.onFinish?.(visit);
    });
}
