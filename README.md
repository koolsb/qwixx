# Qwixx Scoresheets

A digital scoresheet for the [Qwixx](https://gamewright.com/product/Qwixx) dice game.
Replaces the paper pad: tap numbers to cross them out, the left-to-right rule is
enforced automatically, scores tally live, and the game announces when it's over.
A hard refresh (or a crashed tab) restores the sheet exactly as it was.

Built with **Laravel 13 · Flux UI Pro · Tailwind 4 · Alpine.js**. No database, no
login — the game runs entirely client-side and persists to `localStorage`; Laravel
serves the shell and the layout library.

## Features

- **Three sheet layouts** (Classic, Mixed Numbers, Mixed Colors), selectable from the
  picker. Layouts are pure config — see below to add more.
- **Rules enforced**: crosses go left to right; skipped cells are struck out; the
  final cell needs 5 crosses and earns the lock + bonus mark; tapping your most
  recent cross undoes it (mistake correction).
- **Two modes**:
  - **Solo** — one sheet, sized for iPad/phone landscape, for playing along with a
    physical game.
  - **2 players** — both sheets on one iPad lying flat between the players, the top
    sheet rotated 180°. Locking a row locks it for the other player automatically.
- **Rows locked elsewhere**: the small circled button at a row's end marks a row
  locked by a player on paper. Per the rules' simultaneous-lock clause, a player
  with 5+ crosses can still take the final cell of a freshly locked row.
- **Game over** banner when two rows are locked or four penalties are taken.
- **Screen wake lock** on by default (toggleable) so the iPad doesn't sleep mid-game.
- **Reset** with a confirmation dialog.

## How it works

- **Layouts** live in [`config/qwixx.php`](config/qwixx.php); `LayoutFactory`
  validates them at load (4 rows, permutations of 2–12, each color owning exactly
  11 cells and one lock) and `LayoutLibrary` exposes them. The game page embeds the
  layout as JSON for the client.
- **Rules engine** is pure JavaScript in
  [`resources/js/engine.js`](resources/js/engine.js) — state transitions, guards,
  scoring, game-over detection. Scoring always groups marks by *cell color*, which
  makes classic and mixed-color sheets score identically by construction.
- **UI glue** is Alpine.js in [`resources/js/app.js`](resources/js/app.js): taps call
  the engine, every mutation is saved to `localStorage` (`qwixx.game.v1`), and the
  sheet re-renders from derived state.

### Adding a layout

Add an entry to `config/qwixx.php` and redeploy. Rows come in two shapes:

```php
['color' => 'red', 'numbers' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]]   // solid row
['lock' => 'red', 'cells' => [[2, 'yellow'], [3, 'blue'], /* ... */]]   // per-cell colors
```

Validation fails loudly on boot if a layout is malformed.

## Local development

Flux Pro is a licensed package — configure the credentials once:

```bash
composer config http-basic.composer.fluxui.dev "<flux-username>" "<flux-license-key>"
```

Then:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
composer run dev          # serve + vite
```

Open http://localhost:8000.

### Tests & formatting

```bash
php artisan test          # Pest (layout domain + pages)
npm run test              # Vitest (rules engine)
vendor/bin/pint           # format
```

## Deployment

Push to `main` →

1. **CI** (`.github/workflows/ci.yml`) runs Vitest + Pest + Pint.
2. **Build** (`.github/workflows/docker-publish.yml`) builds a multi-arch FrankenPHP
   image and pushes `ghcr.io/koolsb/qwixx:main`.
3. In the **kools-k3s** GitOps repo, `argocd-image-updater` bumps the digest and
   ArgoCD deploys via the shared `charts/laravel` Helm chart.

### Required GitHub repo secrets

| Secret | Used by |
| --- | --- |
| `FLUX_USERNAME` | CI + image build (Flux Pro) |
| `FLUX_LICENSE_KEY` | CI + image build (Flux Pro) |

`GITHUB_TOKEN` (automatic) is used to push to GHCR.

### Cluster (kools-k3s repo)

Mirror the phase10 setup: manifests at `apps/qwixx/` (values) and
`apps/templates/qwixx.yaml` (the ArgoCD Application). Set the hostname, seal
`APP_KEY` and a GHCR pull secret, then flip `qwixx.enabled: true`.

The container serves on **:8080** (non-root), exposes `/health.php` for probes, and
needs **no PVC and no database** — game state lives in each device's browser.
