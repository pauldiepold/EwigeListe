---
name: blade-to-inertia-migration
description: Migriert eine Blade-Seite zu Inertia + Vue 3 in diesem Projekt. Anwenden wenn eine bestehende Blade-Route / ein Controller auf Inertia umgestellt werden soll, eine neue Page unter resources/js/inertia/Pages/ erstellt wird, oder Fragen zu Laravel Resources, Vue-Komponentenschnitt oder Composables im Migrationskontext entstehen.
---

# Blade → Inertia Migration

Referenz: `docs/inertia-migration-plan.md`, Zielstruktur unter `resources/js/inertia/`.

## Checkliste pro Seite

- [ ] Controller: `Inertia::render()` statt `view()`, Resource-Klassen übergeben
- [ ] Resources: Mapping-Logik raus aus dem Controller
- [ ] Page: `Pages/<Name>/Index.vue` erstellen
- [ ] Komponenten: Seite in fokussierte Teilkomponenten schneiden
- [ ] Typen: Zentralisiert in `types/<name>.ts`
- [ ] Composable: Wiederverwendbare Logik (Charts, Fetch, …) auslagern
- [ ] Lint + Type-Check: `npm run type-check && npm run lint` muss grün sein
- [ ] Routen: keine hardcodierten Pfade; Ziggy über Composable `useAppRoute()` und benannte Laravel-Routen

---

## Routen (Ziggy)

- Backend: Routen mit `->name('…')` benennen; Ziggy wird per Inertia in `HandleInertiaRequests` als `ziggy` geteilt.
- Frontend: `import { useAppRoute } from '@/composables/useAppRoute'` → `const { route } = useAppRoute()` → z. B. `route('rounds.create')`, `route('charts.home', { group: id })`.
- Abhängigkeit: `tightenco/ziggy` (Composer) + `ziggy-js` (npm).

## 1. Controller

Nur laden und delegieren – keine Mapping-Logik, keine privaten Methoden.

```php
use App\Http\Resources\FooResource;
use App\Http\Resources\FooItemResource;
use Inertia\Inertia;
use Inertia\Response;

public function index(): Response
{
    $foo = Foo::findOrFail(1);
    $items = auth()->check() ? Item::with('relation')->get() : collect();

    return Inertia::render('Foo/Index', [
        'foo'   => new FooResource($foo),
        'items' => FooItemResource::collection($items),
    ]);
}
```

## 2. API Resources

`$wrap = null` verhindert die `data`-Verschachtelung im Frontend.
Transformationslogik (Feld umbenennen, Links auflösen, Datum formatieren) gehört hier rein – nicht in den Controller.

```php
class FooResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'label' => $this->name,
            'path'  => $this->path(),
        ];
    }
}
```

Hilfsfunktionen wie `printDate()` sind global verfügbar und dürfen in Resources genutzt werden.

## 3. Ordnerstruktur (Frontend)

```
resources/js/inertia/
  Pages/
    Foo/
      Index.vue          ← Page: nur Props + Komponentenmontage
  Components/
    Foo/
      FooList.vue        ← fokussierte Teilkomponente
      FooChart.vue       ← Chart-Wrapper
  composables/
    useFooChart.ts       ← Chart-Logik, fetch, reaktiver State
  types/
    foo.ts               ← alle Typen für diese Seite zentralisiert
```

## 4. Page-Komponente

Schlank halten: Props definieren, Komponenten montieren, kein Business-Logik.

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FooList from '@/Components/Foo/FooList.vue';
import type { FooData, FooItem } from '@/types/foo';

defineProps<{
  foo: FooData;
  items: FooItem[];
}>();
</script>

<template>
  <Head title="Foo" />
  <AppLayout title="Foo">
    <FooList :items="items" />
  </AppLayout>
</template>
```

## 5. Typen

Eine Datei pro Seite unter `types/<name>.ts`:

```ts
export type FooData = { id: number; label: string };
export type FooItem = { id: number; path: string; title: string };
```

In Komponenten per `import type` einbinden – nie inline in `defineProps` tippen.

## 6. Composables (z. B. für Charts)

```ts
// composables/useFooChart.ts
export function useFooChart(canvasRef: Ref<HTMLCanvasElement | null>, id: number) {
  // fetch statt axios für einfache GETs
  const response = await fetch(`/charts/foo/${id}`, { headers: { Accept: 'application/json' } });
  // Chart.js instanziieren, onBeforeUnmount aufräumen
}
```

**fetch vs. axios:** `fetch` für einfache GET-Requests. Axios nur wenn Interceptors, Uploads oder komplexe Fehlerbehandlung gebraucht werden.

## 7. Häufige Fallstricke

| Problem | Lösung |
|---|---|
| `data`-Wrapping im Frontend | `public static $wrap = null;` in der Resource |
| Mapping-Logik im Controller | In Resource-Klasse verschieben |
| Monolithische Page-Komponente | In Teilkomponenten unter `Components/<Name>/` aufteilen |
| Typen dupliziert | Zentralisiert in `types/<name>.ts`, per `import type` einbinden |
| Chart-Animation-Fehler | `animation: false` statt `animation.duration = 0` (TS-safe) |
| Features die halbfertig sind | Nicht in Migration einschließen, separat dokumentieren |

## 8. Validierung

Nach jeder Seite:
```bash
npm run type-check && npm run lint
```

Beides muss ohne Fehler durchlaufen, bevor die Seite als `done` gilt.
