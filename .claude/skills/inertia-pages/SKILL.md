---
name: inertia-pages
description: Konventionen dieses Projekts für Inertia-Seiten mit Vue 3 und Nuxt UI. Anwenden beim Anlegen oder Ändern einer Page oder Komponente unter `resources/js/inertia/`, eines Controllers mit `Inertia::render()`, einer Resource unter `app/Http/Resources/`, beim Schreiben von Feature- oder Komponententests (Vitest) und bei Meldungen nach Aktionen (Toast).
---

# Inertia-Seiten

Begriffe: `GLOSSARY.md`. Warum etwas so gebaut ist: die Entscheidungen in `docs/migration.md` und `docs/adr/`.

## Ablauf

1. **Roter Test.** Feature-Test mit `assertInertia` für Component und Props schreiben und laufen lassen: er muss _rot_ sein. Jede angefasste Mutation (POST/PATCH/DELETE) bekommt Tests für Redirect, Validierungsfehler und Berechtigung. Neues Verhalten rein im Client bekommt einen Komponententest (siehe unten).
2. **Backend.** Controller rendert per `Inertia::render()` und lädt nur, was die Page braucht. Mapping steckt in `app/Http/Resources/*`.
3. **Frontend.** Page, Komponenten, Typen und Composables nach der Struktur unten.
4. **Grün.** Fertig ist die Änderung, wenn alles grün ist:
   ```bash
   php artisan test --compact
   npm run test:js && npm run type-check && npm run lint && npm run format
   ```

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

- PHPUnit: `#[Test]`-Attribut und snake_case-Namen; `/** @test */` wird nicht erkannt, solche Tests laufen still nicht.
- Factories über `Model::factory()`. Die Ewige Liste (`Group::EWIGE_LISTE_ID`) legt `Tests\TestCase` in jedem Test mit `RefreshDatabase` an; eigene Listen bekommen also IDs ab 2. Echte Spieler sind immer Mitglied der Ewigen Liste (`Group::find(Group::EWIGE_LISTE_ID)->addPlayer($player)`).
- Der erste Benutzer einer Factory hat ID 1 und ist damit Admin (`Gate::before`); Berechtigungstests brauchen einen zweiten Benutzer. Benutzer- und Spieler-IDs laufen auseinander, nie gleichsetzen.
- Immer `assertOk()` vor `assertSee`, sonst besteht der Test auch mit einer Fehlerseite, in deren Stacktrace der Text steht.
- `assertInertia` prüft, dass die Page-Datei unter `resources/js/inertia/Pages` existiert (`config/inertia.php`).
- Props aus einer Resource-Collection mit `->resolve(request())` liegen direkt unter dem Prop-Namen (`groups.0`), nicht unter `groups.data`.
- reCAPTCHA: `NoCaptcha::shouldReceive('verifyResponse')->andReturn(true)`.
- Vite ist in `Tests\TestCase` abgeschaltet; Feature-Tests brauchen kein `npm run build`.

## Komponententests

Vitest mit jsdom und Testing Library. Ein Komponententest entsteht **test-first** in zwei Fällen:

- neues Verhalten rein im Client, das kein Feature-Test sieht: Dialog- und Formularlogik, lokaler Zustand, Reaktion auf Echtzeit-Updates, Live-Spiel;
- ein Bug im Client: erst der Test, der den Bug _rot_ zeigt, dann der Fix.

Bestehendes, abgenommenes Verhalten bleibt ohne nachgezogene Tests; Layout und Styling prüft Paul. Testet ein Test vorhandenes Verhalten (z. B. beim Bug-Fix ein Nachbarfall), zeigt eine kurze Mutation der Komponente, dass er _rot_ werden kann.

Referenz: `Components/Rounds/__tests__/EnterGameModal.test.ts`. Einzeln laufen lassen: `npm run test:js -- EnterGameModal`.

- **Ablage:** `__tests__/<Komponente>.test.ts` im Ordner der Komponente, Helfer in `resources/js/inertia/testing/`. Testnamen beschreiben Verhalten auf Deutsch (`schickt ohne gültige Auswahl nichts ab`).
- **Rendern:** `renderWithUi(Component, props)` hängt Nuxt UI und `UApp` darum. Abfragen über `screen`, weil Modals und Toasts in `document.body` teleportieren. Neue Props per `await rerender({ … })`.
- **Abfragen:** nach Rolle, Label und Text (`getByRole('button', { name: 'Speichern' })`) statt über Klassen, die sich beim nächsten Redesign ändern; Zustand über ARIA (`aria-pressed`), Interaktion per `userEvent.setup()`. DOM-Matcher aus jest-dom sind nicht installiert: `textContent` und Attribute direkt prüfen.
- **Requests:** `useForm` bleibt echt. `fakeVisits('post', { errors })` bzw. `fakeVisits('patch')` ersetzt nur den Versand und spielt die Router-Callbacks durch; der zurückgegebene Spy zeigt URL und Daten. Erwartete URLs als Literal (`'/rounds/7/games'`), nicht über Wayfinder nachgerechnet.
- **Pages:** `usePage` und `<Head>` brauchen den Zustand, den nur `createInertiaApp` anlegt. Ein Page-Test ersetzt beide per Teil-Mock; `url` braucht `AppLayout`. Mit dem ersten Page-Test wandert das Mock als Helfer nach `testing/`:

  ```ts
  const page = vi.hoisted(() => ({
    url: '/rounds/create',
    props: { auth: { user: { id: 1 }, player: { id: 5, fullName: 'Paul D.' } }, navigation: { currentRoundId: null } },
    flash: {},
  }));
  vi.mock('@inertiajs/vue3', async (importOriginal) => ({
    ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
    usePage: () => page,
    Head: { render: () => null },
  }));
  ```

  `vi.hoisted`, weil `vi.mock` vor alle Imports gehoben wird. Flash aus einer Antwort kommt über `fakeVisits('post', { page: { flash: { … } } })` in `onSuccess`.
- **Live-Spiel:** Seiten-Tests ersetzen `@/composables/useLiveEcho` (Echo), `@/composables/liveRequest` (`postLive`) und `@/composables/reloadPage` per `vi.mock`; Helfer in `testing/live.ts` und `testing/liveEcho.ts`. Fake-Timer erst nach dem Rendern einschalten.
- **jsdom-Lücken** wie `matchMedia` schließt `testing/setup.ts` für alle Tests.
- **Wayfinder:** Tests importieren die generierten Dateien unter `@/routes/...`; sie entstehen per `npm run build`. Fehlen sie in einem frischen Checkout, zuerst bauen.

## Backend

```php
public function index(): Response
{
    $items = auth()->check() ? Item::with('relation')->get() : collect();

    return Inertia::render('Foo/Index', [
        'items' => FooItemResource::collection($items)->resolve(request()),
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

- Props in camelCase (`avatarPath`, `playerId`), Spielernamen nach Bedeutung (`firstName`, `lastName`, `fullName`, `displayName`).
- Neue Routen bekommen einen Namen (`->name(...)`), damit Wayfinder sie unter `@/routes/...` erzeugt.
- Daten, die nur ein Tab oder ein Chart braucht, per `Inertia::optional` und im Frontend `WhenVisible`.

## Meldungen nach Aktionen (Toast)

Erfolgs- und Fehlermeldungen nach einem Redirect laufen als Inertia-Flash und erscheinen zentral als Toast (`Components/Ui/FlashToasts.vue` in `AppRoot.vue`). Pages zeigen sie **nicht** selbst an.

```php
Toast::success('Name wurde erfolgreich gespeichert!'); // App\Http\Toast
return redirect()->route('users.edit', $user);

Toast::error('Du kannst nur das letzte Spiel einer Runde löschen!');
return back();
```

- Nur `success` und `error`; die Meldung kommt immer über `App\Http\Toast`, nie über `->with(...)` oder `usePage().props.flash`.
- Abgelehnte Aktionen (keine Berechtigung, unzulässiger Zustand) sind `Toast::error()`, nicht `withErrors([...])` ohne Feld.
- Validierungsfehler bleiben `errors` und werden in der Page angezeigt: am `UFormField` oder, ohne passendes Eingabefeld, als `UAlert` (Beispiel `Rounds/Create`).
- Test: `->assertInertiaFlash('toast', ['type' => 'success', 'message' => '…'])` auf der Redirect-Antwort, siehe `tests/Feature/FlashToastTest.php`.

## Laden und Seitenwechsel

- Keine Skeletons, Instant Visits oder Prefetch-Konfiguration vorab; als Rückmeldung reicht die Fortschrittsleiste (#57).
- Ein Prop wird nur deferred, wenn er spürbar langsamer ist als der Rest **und** für die erste Ansicht entbehrlich (Charts, Statistiken). Daten, mit denen die Seite arbeitet, nie deferren, sondern die Query schneller machen. Beispiel: `homeGamesChart` auf Home.

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
- Formulare: `useForm` aus `@inertiajs/vue3`, Submit mit `form.post(store.url())`, Fehler an `UFormField` binden. Das HTTP-Verb bleibt explizit, weil sich GET- und POST-Route oft eine URL teilen (`login`).
- URLs über Wayfinder mit benannten Imports, z. B. `import { show as showGroup } from '@/routes/groups'` und `showGroup.url(group.id)`; Query-Parameter per `index.url(id, { query: { page } })`. Bei Namenskonflikten mit lokalen Funktionen per `as` umbenennen (`logout as logoutRoute`).
- Requests ohne Seitenwechsel: `fetch` für einfache GETs, sonst der HTTP-Client von Inertia (Beispiel `composables/liveRequest.ts`). Axios ist nicht installiert.
- Echtzeit nur über `composables/echoConnection.ts`.
- Icons ausschließlich `i-lucide-*`. Für OAuth-Buttons (Google, Facebook) generische Icons wie `i-lucide-globe` oder nur Text.

## Fallstricke

| Problem | Lösung |
|---|---|
| `data`-Verschachtelung im Frontend | `public static $wrap = null;` in der Resource, Collections mit `->resolve(request())` |
| `UTable` + `paginate()` | Nur die aktuelle Seite liegt im Browser. Sortieren/Filtern serverseitig (Query-Params, Sort-Spalten auf Whitelist, Tie-Breaker wie `orderByDesc('id')`), im Frontend `sorting-options: { manualSorting: true }`, bei Seitenwechsel dieselben `sort`/`direction`-Params mitschicken. Referenz: `RoundController@index`, `Pages/Rounds/Index.vue`, `Components/Rounds/ArchiveRoundsTable.vue` |
| `WhenVisible` lädt nicht neu | lädt pro Komponenten-Instanz nur einmal; bei `preserveState` einen `key` je Liste setzen |
| `useRemember` nach Redirect leer | gemerkter State hängt am History-Eintrag; Zustand nur ohne Navigation erhalten (Modal mit `preserveState`) |
| Chart.js-Animation in TS | `animation: false` statt `animation.duration = 0` |
