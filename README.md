# filaas

The Laravel SaaS starter kit you're already looking at.

[filaas.com](https://filaas.com) · [GitHub](https://github.com/filaascom/filaas)

```bash
composer create-project filaascom/filaas my-app
```

Auth, multi-tenant teams, Stripe billing, Filament v5 admin panel, PWA + Web Push, Reverb real-time, Pest 4 tests — all wired up. Every Laravel SaaS needs the same boring foundation. Built once, tested, drop in your product.

## What's inside

- **Laravel 13** + PHP 8.4
- **Filament v5** — admin panel, clusters for Account / Team / Billing already shipped
- **Livewire v4** + Tailwind v4
- **Cashier 16** — Stripe Checkout, customer portal, webhooks signed and tested. Two plans (`starter`, `pro`) × two intervals (monthly, yearly) wired through `config/billing.php`
- **Multi-tenant teams** — owners, members, invitations by email, ownership transfer, scheduled account deletion with grace period
- **PWA** — service worker, manifest, install prompt, offline page, VAPID push subscriptions
- **Reverb** — native Laravel WebSockets. Broadcast jobs, presence channels, no third-party bill
- **Pest 4** — feature + browser smoke + arch tests covering auth, teams, invitations, billing
- **AI-ready** — Laravel Boost (MCP) and `CLAUDE.md` pre-configured. Open Claude Code and ship features, not setup
- **Plus** imports/exports with row-level error tracking, PDFs (Browsershot + Spatie), sitemap, Nightwatch observability, queues, scheduling, SEO meta partials

## Local development

After `composer create-project`:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
composer run dev    # serves app + queue + logs + vite concurrently
```

SQLite by default — zero config to look around. Default panel: `/app`. Marketing landing: `/`.

### Stripe

Plug your price IDs into `.env`:

```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRICE_STARTER_MONTHLY=
STRIPE_PRICE_STARTER_YEARLY=
STRIPE_PRICE_PRO_MONTHLY=
STRIPE_PRICE_PRO_YEARLY=
```

Webhook endpoint: `/stripe/webhook`. Plans live in `config/billing.php`.

### Web Push (VAPID)

```bash
php artisan webpush:vapid    # generates keys
```

Paste `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` into `.env`.

### Tests

```bash
php artisan test --compact
```

Browser tests use Pest 4 + Playwright (auto-installed via Pest plugin).

## Pricing

Free until your project earns. Three tiers, **same code on every tier — only the license changes**:

- **Free** — for projects under $1k/month. MIT-style license up to the cap.
- **Pro** — $299 one-time, for projects up to $50k MRR. Commercial license, 1 year of updates.
- **Studio** — $999 one-time, unlimited projects, lifetime updates, white-label rights.

Buy at [filaas.com/#pricing](https://filaas.com/#pricing).

## License

MIT-style up to your tier's cap, plus a commercial addendum for paid tiers. Plain English in `LICENSE`.
