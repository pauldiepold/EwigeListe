---
name: blade-to-inertia-migration
description: Migriert eine Blade-Seite per TDD zu Inertia + Vue 3 (Big Bang, `docs/migration.md`). Anwenden, wenn eine Route von `view()` auf `Inertia::render()` umgestellt, eine Page unter `resources/js/inertia/Pages/` angelegt oder Legacy (Blade, `resources/js/components/**`) einer migrierten Seite gelöscht wird.
---

# Blade → Inertia Migration

Plan, Leitplanken und Routen-Matrix: `docs/migration.md`. Begriffe: `GLOSSARY.md`. Die Details der Seite stehen im zugehörigen Issue.

## Ablauf pro Seite

1. **Spezifikation lesen.** Die Blade-Datei und alle darin eingebundenen Legacy-Vue-Komponenten sind die Spezifikation. Notiere, welche Variablen das Template wirklich nutzt und welche Kerninteraktionen (Laden, Navigation, Speichern, Validierung) die Seite hat. Weicht die Routen-Matrix ab, zählt die Blade; die Matrix-Zeile wird nachgezogen.
2. **Roter Test.** Feature-Test mit `assertInertia` für Component und Props schreiben und laufen lassen: er muss _rot_ sein. Jede angefasste Mutation (POST/PATCH/DELETE) bekommt Tests für Redirect, Validierungsfehler und Berechtigung.
3. **Backend.** Controller rendert per `Inertia::render()` und lädt nur, was die Page braucht. Mapping steckt in `app/Http/Resources/*`. Ungenutzte Controller-Daten werden gelöscht.
4. **Frontend.** Page, Komponenten, Typen und Composables nach der Struktur unten.
5. **Legacy löschen.** Blade-Datei und Legacy-Vue-Komponenten, die nur diese Seite nutzte, im selben PR entfernen. Vorher per Grep belegen, dass keine Route, kein `@include`/`@extends` und keine Registrierung in `resources/js/app.js` mehr darauf verweist.
6. **Status pflegen.** Routen-Matrix und ggf. Roadmap in `docs/migration.md` aktualisieren; neue Erkenntnisse unter „Learnings“.
7. **Grün.** Fertig ist die Seite, wenn alles grün ist:
   ```bash
   php artisan test --compact
   npm run type-check && npm run lint && npm run format:fix
   ```

Links auf noch nicht migrierte Seiten dürfen scheitern (Big Bang); dafür werden keine Brücken gebaut. Backend-Modernisierung, die die Seite nicht braucht, landet als Kommentar in #38 statt im PR.

## Tests

Referenzen: `tests/Feature/StaticPagesTest.php` (mehrere Routen per Data Provider), `tests/Feature/ManageGroupsTest.php` (Props, Mutation, Validierung).

```php
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;

#[Test]
public function a_user_sees_the_group_list(): void
{
    $this->actingAs(User::factory()->create());
    $group = Group::factory()->create();

    $this->get(route('groups.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Groups/Index')
            ->has('groups', 1)
            ->where('groups.0.name', $group->name)
        );
}

#[Test]
public function a_group_requires_a_name(): void
{
    $this->actingAs(User::factory()->create());

    $this->post(route('groups.store'), ['name' => ''])->assertSessionHasErrors('name');
}
```

- PHPUnit 12: `#[Test]`-Attribut und snake_case-Namen; `/** @test */` wird nicht mehr erkannt.
- Factories über `Model::factory()`. Die Ewige Liste (`Group::EWIGE_LISTE_ID`) legt `Tests\TestCase` in jedem Test mit `RefreshDatabase` an; eigene Listen bekommen also IDs ab 2. Echte Spieler sind immer Mitglied der Ewigen Liste (`Group::find(Group::EWIGE_LISTE_ID)->addPlayer($player)`).
- Immer `assertOk()` vor `assertSee`, sonst besteht der Test auch mit einer Fehlerseite, in deren Stacktrace der Text steht.
- `assertInertia` prüft, dass die Page-Datei unter `resources/js/inertia/Pages` existiert (`config/inertia.php`).
- Props mit Resource-Collection ohne Wrapper (`$wrap = null`) liegen direkt unter dem Prop-Namen (`groups.0`), nicht unter `groups.data`.
- reCAPTCHA: `NoCaptcha::shouldReceive('verifyResponse')->andReturn(true)`.
- Vite ist in `Tests\TestCase` abgeschaltet; Tests brauchen kein `npm run build`.

## Backend

```php
public function index(): Response
{
    $items = auth()->check() ? Item::with('relation')->get() : collect();

    return Inertia::render('Foo/Index', [
        'items' => FooItemResource::collection($items),
    ]);
}
```

```php
class FooItemResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->name,
            'path' => $this->path(),
        ];
    }
}
```

Helfer wie `printDate()` sind global und dürfen in Resources genutzt werden. Neue Routen bekommen einen Namen (`->name(...)`), damit Ziggy sie kennt.

## Frontend

```
resources/js/inertia/
  Pages/Foo/Index.vue          ← nur Props + Komponentenmontage
  Components/Foo/FooList.vue   ← fokussierte Teilkomponenten
  composables/useFooChart.ts   ← wiederverwendbare Logik (fetch, Charts, State)
  types/foo.ts                 ← Typen der Seite, per `import type`
```

- Page schlank halten; sobald sie wächst, in Komponenten unter `Components/<Bereich>/` schneiden.
- `types/<name>.ts` nur, wenn die Page Props oder geteilte Typen hat; Typen nie inline in `defineProps`.
- Formulare: `useForm` aus `@inertiajs/vue3`, Submit mit `form.post(route('…'))`, Fehler an `UFormField` binden.
- URLs über `const { route } = useAppRoute()`, z. B. `route('charts.home', { group: id })`.
- `fetch` für einfache GETs; axios nur für Interceptors, Uploads oder komplexe Fehlerbehandlung.
- Icons ausschließlich `i-lucide-*`. Für OAuth-Buttons (Google, Facebook) generische Icons wie `i-lucide-globe` oder nur Text.
- Look nah am Bestand halten, Struktur sauber: das Design kommt gebündelt in Phase 4.

## Fallstricke

| Problem | Lösung |
|---|---|
| `data`-Verschachtelung im Frontend | `public static $wrap = null;` in der Resource |
| `UTable` + `paginate()` | Nur die aktuelle Seite liegt im Browser. Sortieren/Filtern serverseitig (Query-Params, Sort-Spalten auf Whitelist, Tie-Breaker wie `orderByDesc('id')`), im Frontend `sorting-options: { manualSorting: true }`, bei Seitenwechsel dieselben `sort`/`direction`-Params mitschicken. Referenz: `RoundController@index`, `Pages/Rounds/Index.vue`, `Components/Rounds/ArchiveRoundsTable.vue` |
| Positionsbasiertes JSON mit HTML (`Group::records`/`stats`) | Siehe `docs/tech-debt/group-positional-json.md` und #39 |
| Chart.js-Animation in TS | `animation: false` statt `animation.duration = 0` |
| Halbfertige Legacy-Features | Nicht mitmigrieren; als Issue mit Label `nach-migration` festhalten |
