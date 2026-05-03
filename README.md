# filaas

The Laravel SaaS starter kit you're already looking at.

[filaas.com](https://filaas.com) · [GitHub](https://github.com/filaascom/filaas)

```bash
composer create-project filaascom/filaas my-app
```

Auth, multi-tenant teams, per-team Stripe subscriptions, Filament v5 admin panel, PWA + Web Push, Pest 4 browser tests — all wired up. The marketing landing on `/` is built from the same Filament `Component` classes as the panel. Drop in your product.

## What's inside

- **Laravel 13** + PHP 8.4
- **Filament v5** — admin panel under `/app`, clusters for Account / Team / Billing already shipped. The marketing landing on `/` uses the same `Component` system.
- **Livewire v4** + **Tailwind v4**
- **Cashier 16** — Stripe Checkout, customer portal, signed webhooks. Three tiers (`free`, `pro`, `studio`) × two intervals (monthly, yearly), wired through `config/billing.php`. Subscriptions are attached to the **team**, not the user.
- **Multi-tenant teams** — owners, administrators, managers, invitations by email, ownership transfer, scheduled account deletion with a 30-day grace period and a daily prune job.
- **PWA** — service worker, manifest, install prompt, offline page, VAPID web-push subscriptions.
- **Pest 4** — feature, arch, and **browser tests** (via `pest-plugin-browser`) covering registration, login, account settings, team details, members, invitations, ownership transfer, and billing flows. Plus a smoke pass over every panel route.
- **AI-ready** — Laravel Boost (MCP) and `CLAUDE.md` pre-configured with Pest, Filament, Tailwind, Cashier, and Nightwatch skills.
- **Plus** — avatar + team-logo uploads, RBAC policies, password-reset emails, sitemap, SEO + OpenGraph meta partials, Nightwatch observability, dark mode, scheduled `users:prune` job.

## Local development

After `composer create-project`, the post-create hook generates `APP_KEY`, creates a SQLite database file, and runs migrations. You still need to:

```bash
cp .env.example .env             # if not copied already
php artisan storage:link         # so uploaded avatars/logos resolve
npm install && npm run build
```

Then either run components individually, or:

```bash
composer run dev    # serves app + queue listener + log tailer + vite, all concurrent
```

SQLite by default — zero config to look around. Default panel: `/app`. Marketing landing: `/`.

> First time setting up after a clean clone (without `composer create-project`)? Run `composer run setup` — it copies `.env`, generates the key, migrates, installs npm deps, and builds assets in one shot.

### Stripe

Plug your keys and price IDs into `.env`:

```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

STRIPE_PRODUCT_PRO=
STRIPE_PRICE_PRO_MONTHLY=
STRIPE_PRICE_PRO_YEARLY=

STRIPE_PRODUCT_STUDIO=
STRIPE_PRICE_STUDIO_MONTHLY=
STRIPE_PRICE_STUDIO_YEARLY=
```

The `free` tier needs no Stripe IDs. Plans, descriptions, and feature lists live in `config/billing.php`.

Cashier registers `POST /stripe/webhook` automatically — point your Stripe dashboard at `https://your-domain/stripe/webhook` and copy the signing secret into `STRIPE_WEBHOOK_SECRET`.

Locally, forward webhooks with the Stripe CLI:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

### Web Push (VAPID)

Generate VAPID keys once:

```bash
php artisan webpush:vapid
```

Paste the printed `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` into `.env`. The `VAPID_SUBJECT` should be a `mailto:` address (already set in `.env.example`). Service worker lives at `/sw.js`, manifest at `/manifest.webmanifest`.

### Mail

`MAIL_MAILER=log` by default — invitations and password-reset emails land in `storage/logs/laravel.log`. Switch to your provider in `.env` for real delivery.

### Scheduled tasks

`routes/console.php` schedules `users:prune` daily — it permanently deletes users whose `deleted_at` is older than 30 days (configurable with `--days=N`). Wire your scheduler:

```cron
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

### Tests

```bash
php artisan test --compact                       # everything
php artisan test --compact tests/Browser         # only browser tests
```

Browser tests use Pest 4 + the `pest-plugin-browser` package, which drives a real Chromium. The first run downloads the browser binary automatically.

## Pricing

Free until your project earns. Three tiers, **same code on every tier — only the license changes**:

- **Free** — for projects under $1k/month MRR. MIT-style license up to the cap.
- **Pro** — $29/month or $278/year, up to $10k MRR. Commercial license, continuous updates while subscribed.
- **Studio** — $79/month or $758/year, no revenue cap. Adds white-label rights and unlimited projects.

Buy at [filaas.com/#pricing](https://filaas.com/#pricing).

## License

MIT-style up to your tier's cap, plus a commercial addendum for paid tiers. Plain English in `LICENSE`.
