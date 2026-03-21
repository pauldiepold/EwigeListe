import { usePage } from '@inertiajs/vue3';
import { route as ziggyRoute } from 'ziggy-js';
import type { ZiggyPageConfig } from '@/types/ziggy-config';

type ZiggyPageProps = {
  ziggy?: ZiggyPageConfig;
};

/**
 * Laravel benannte Routen via Ziggy (Inertia shared prop `ziggy`).
 * Liest bei jedem Aufruf die aktuelle Config aus `usePage()` (nach Navigation korrekt).
 */
export function useAppRoute() {
  const page = usePage<ZiggyPageProps>();

  function route(
    name: string,
    params?: Record<string, string | number | boolean> | number | string,
    absolute = false,
  ): string {
    const config = page.props.ziggy;
    if (!config) {
      throw new Error('Ziggy-Konfiguration fehlt: Inertia-Prop "ziggy" ist nicht gesetzt.');
    }
    // ziggy-js erwartet enge Routen-Param-Typen; hier bewusst generisch (Laravel liefert zur Laufzeit korrekte Keys).
    return String(ziggyRoute(name, params as never, absolute, config as never));
  }

  return { route };
}
