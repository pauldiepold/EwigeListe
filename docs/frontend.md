# Frontend: Regeln der EwigeListe

Ergänzt den Skill `inertia-pages` (aus dem Home Repo) um alles, was nur hier gilt. Wo diese Datei abweicht, hat sie Vorrang.

## Feature-Tests

Referenzen: `tests/Feature/StaticPagesTest.php` (mehrere Routen per Data Provider), `tests/Feature/ManageGroupsTest.php` (Props, Mutation, Validierung), `tests/Feature/FlashToastTest.php` (Toast).

- PHPUnit: `#[Test]`-Attribut (`PHPUnit\Framework\Attributes\Test`) und snake_case-Namen auf Englisch (`a_group_requires_a_name`). `/** @test */` wird nicht erkannt, solche Tests laufen still nicht.
- Die Ewige Liste (`Group::EWIGE_LISTE_ID`) legt `Tests\TestCase` in jedem Test mit `RefreshDatabase` an; eigene Listen bekommen also IDs ab 2. Echte Spieler sind immer Mitglied der Ewigen Liste (`Group::find(Group::EWIGE_LISTE_ID)->addPlayer($player)`).
- Der erste Benutzer einer Factory hat ID 1 und ist damit Admin (`Gate::before`); Berechtigungstests brauchen einen zweiten Benutzer. Benutzer- und Spieler-IDs laufen auseinander, nie gleichsetzen.
- reCAPTCHA: `NoCaptcha::shouldReceive('verifyResponse')->andReturn(true)`.

## Komponententests

Referenz: `Components/Rounds/__tests__/EnterGameModal.test.ts`. Helfer unter `resources/js/inertia/testing/`: `renderWithUi.ts`, `fakeVisits.ts`, `setup.ts`, für das Live-Spiel `live.ts` und `liveEcho.ts`.

- Das Page-Mock braucht hier `auth.player` und `navigation`, z. B. `props: { auth: { user: { id: 1 }, player: { id: 5, fullName: 'Paul D.' } }, navigation: { currentRoundId: null } }`.
- **Live-Spiel:** Seiten-Tests ersetzen `@/composables/useLiveEcho` (Echo), `@/composables/liveRequest` (`postLive`) und `@/composables/reloadPage` per `vi.mock`. Fake-Timer erst nach dem Rendern einschalten.

## Backend

- Spielernamen in Props nach Bedeutung: `firstName`, `lastName`, `fullName`, `displayName`.
- Beispiel für einen deferred Prop: `homeGamesChart` im `HomeController`.

## Frontend

- Toasts: `Components/Ui/FlashToasts.vue` hängt in `AppRoot.vue`. Validierungsfehler ohne Eingabefeld als `UAlert`: Beispiel `Pages/Rounds/Create.vue`.
- Requests ohne Seitenwechsel über den HTTP-Client von Inertia: Beispiel `composables/liveRequest.ts`.
- Echtzeit nur über `composables/echoConnection.ts`.
- OAuth-Buttons (Google, Facebook): generische Icons wie `i-lucide-globe` oder nur Text.
- Serverseitig sortierte und paginierte Tabelle: `RoundController@index`, `Pages/Rounds/Index.vue`, `Components/Rounds/ArchiveRoundsTable.vue`.
- Laden und Seitenwechsel: Hintergrund zur Entscheidung gegen Skeletons und Prefetch in #57.
