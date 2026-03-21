/**
 * Von Laravel/Ziggy serialisierte Route-Liste (Inertia shared prop `ziggy`).
 * Entspricht dem Output von `(new Ziggy)->toArray()` plus `location`.
 */
export type ZiggyPageConfig = {
  url: string;
  port: number | null;
  defaults: Record<string, string | number>;
  routes: Record<string, unknown>;
  location?: string | { host?: string; pathname?: string; search?: string };
};
