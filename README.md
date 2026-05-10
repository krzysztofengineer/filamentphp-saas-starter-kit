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

Two layers stay in sync: your **Stripe account** (products + prices) and your **app** (`.env` Stripe IDs + `config/billing.php` UI copy and amounts).

#### 1. Account and API keys

Create a Stripe account at [dashboard.stripe.com/register](https://dashboard.stripe.com/register), or use an existing one. For development, either flip the **Test mode** toggle at the top of the dashboard, or create a dedicated **Sandbox** (Settings → Sandboxes → *Create sandbox*) for full isolation from live data. Test mode and sandboxes both issue separate `pk_test_…` / `sk_test_…` keys.

From **Developers → API keys**, paste into `.env`:

```
STRIPE_KEY=pk_test_…
STRIPE_SECRET=sk_test_…
```

#### 2. Create products and prices

Two products (Pro, Studio), each with a monthly and a yearly recurring price. Click them in the dashboard at **Catalog → Products**, or use the [Stripe CLI](https://docs.stripe.com/stripe-cli):

```bash
stripe products create -d "name=Pro"
stripe products create -d "name=Studio"

stripe prices create -d product=prod_… -d currency=usd -d unit_amount=2900  -d "recurring[interval]=month" -d "nickname=Pro Monthly"
stripe prices create -d product=prod_… -d currency=usd -d unit_amount=27800 -d "recurring[interval]=year"  -d "nickname=Pro Yearly"
stripe prices create -d product=prod_… -d currency=usd -d unit_amount=7900  -d "recurring[interval]=month" -d "nickname=Studio Monthly"
stripe prices create -d product=prod_… -d currency=usd -d unit_amount=75800 -d "recurring[interval]=year"  -d "nickname=Studio Yearly"
```

Paste each `prod_…` and `price_…` into `.env`:

```
STRIPE_PRODUCT_PRO=prod_…
STRIPE_PRICE_PRO_MONTHLY=price_…
STRIPE_PRICE_PRO_YEARLY=price_…

STRIPE_PRODUCT_STUDIO=prod_…
STRIPE_PRICE_STUDIO_MONTHLY=price_…
STRIPE_PRICE_STUDIO_YEARLY=price_…
```

The `free` tier needs no Stripe IDs. Run `php artisan config:clear` after editing `.env`.

#### 3. Edit `config/billing.php` to match

`config/billing.php` is the single source of truth for everything users see — plan key, `name`, `description`, `features` array, `prices.{monthly,yearly}.{amount,label,period}`, `badge`, `highlighted`, and `is_free`. The pricing section on the marketing landing (`/`) reads from it via `PricingSection::make()`, and so does the in-app `/app/{team}/settings/subscription` page.

`amount` is in cents and **must match** the `unit_amount` of the corresponding Stripe price — Stripe is what charges the card; the config is what renders.

To rename, reprice, or add/remove a tier:

1. Edit the entry in `config/billing.php`.
2. Update the matching `STRIPE_PRICE_<KEY>_<INTERVAL>` env variable to point at the new Stripe price.
3. If you renamed the plan key (e.g. `pro` → `team`), add the case in `App\Enums\BillingPlan`.
4. Restart `composer run dev` (or rerun `npm run build`) so Vite picks up Tailwind classes touching any new section.

#### 4. Webhooks

Cashier registers `POST /stripe/webhook` automatically.

**Production** — in **Developers → Webhooks**, add an endpoint at `https://your-domain/stripe/webhook` listening for the `customer.*`, `invoice.*`, and `payment_intent.*` event groups. Copy the endpoint's signing secret into `STRIPE_WEBHOOK_SECRET`.

**Local dev** — forward events to your local app with the Stripe CLI:

```bash
stripe listen --forward-to http://saas.test/stripe/webhook
```

The first line printed (`whsec_…`) is your local signing secret. Drop it into `.env` as `STRIPE_WEBHOOK_SECRET` and keep `stripe listen` running while you test. `php artisan config:clear` after edits.

#### 5. Testing checkout in dev

With `composer run dev` and `stripe listen` both running:

1. Log in (any seeded user — password is `password`).
2. Open the team's **Subscription** page at `/app/{team-uuid}/settings/subscription` and click *Choose plan*.
3. You land on Stripe Checkout. Use a [test card](https://docs.stripe.com/testing#cards):
    - `4242 4242 4242 4242` — succeeds
    - `4000 0027 6000 3184` — requires 3D Secure authentication
    - `4000 0000 0000 9995` — fails with insufficient funds

   Any future expiry, any 3-digit CVC, any postal code.
4. On success you're redirected to `/billing/{team}/success`. Watch `stripe listen`: `customer.subscription.created`, `invoice.paid`, and `payment_intent.succeeded` all return `200`. The team row picks up a `stripe_id`, a row appears in `subscriptions`, and the *Subscription* page now shows *Manage subscription* on the active tier.

Replay an event without going through Checkout:

```bash
stripe trigger customer.subscription.created
```

Headless equivalents live in `tests/Browser/Billing*Test.php` — run with `php artisan test --compact tests/Browser`.

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

## Customizing

### Branding

- App name (panel title, mail-from, manifest) — `APP_NAME` in `.env`
- Brand color across both panels — `brand_color` in `config/app.php` (any `Filament\Support\Colors\Color::*` or hex array)
- Wordmark — `resources/views/components/marketing/wordmark.blade.php`
- Auth-pages mark — `resources/views/components/simple-logo.blade.php`
- Default theme mode — `->defaultThemeMode(...)` in `app/Providers/Filament/{App,Home}PanelProvider.php`
- Marketing landing tokens (colors, fonts) — `resources/css/filament/home/theme.css`

### Landing page

The landing on `/` is the Filament page `app/Filament/Home/Pages/Home.php`. Edit `content()` to change copy or reorder sections. Each section is a class under `app/Filament/Schemas/Components/Marketing/` plus a Blade view under `resources/views/filament/schemas/marketing/`.

`PricingSection` reads `config/billing.php` automatically. Replace `->plans(...)` with your own tiers (or delete `PricingSection::make()`) if you're not selling subscriptions.

### Adding to the admin panel

```bash
php artisan make:filament-resource MyThing
```

Drop it into the panel directly, or attach it to a cluster:

```php
protected static ?string $cluster = TeamSettingsCluster::class;
```

Top-nav, user-menu and tenant-menu items are arrays inside `app/Providers/Filament/AppPanelProvider.php::panel()`.

### Teams & roles

Roles are an enum at `app/Enums/TeamRole.php` (`Administrator`, `Member`). Permission checks live as methods on the enum and the `Team` model — extend both to add a third role.

Every team operation (invite, accept, transfer ownership, change role, leave, delete) is its own action class under `app/Actions/Teams/`.

Invitations don't expire by default. To add expiry, add `expires_at` to `team_invitations` and check it in `app/Policies/TeamInvitationPolicy.php::accept()`.

### Account deletion grace period

Default 30 days. Override with `ACCOUNT_DELETION_GRACE_DAYS` in `.env` (`config/account.php`). The daily `users:prune` command in `routes/console.php` permanently deletes users past the cutoff.

To send a "your account will be deleted" email, hook a notification into `app/Actions/Accounts/ScheduleAccountDeletion.php`.

### PWA

- Manifest (name, icons, theme color, start URL) — `app/Http/Controllers/Pwa/ManifestController.php`
- Icons — `public/icons/icon-{192,512}.png`
- Offline page — `resources/views/pwa/offline.blade.php`

### Avatars & team logos

Filament `FileUpload` definitions live in `AccountSettings::saveAction()` and `TeamDetails::saveAction()`. Disks `user-avatars` and `team-logos` are in `config/filesystems.php`. The default fallback (initials via ui-avatars.com) is in `app/Filament/AvatarProviders/`.

### Mail copy

- Team invitation — `resources/views/mail/team-invitation.blade.php`
- Password reset — `app/Notifications/ResetPassword.php` (no Blade; edit `toMail()`)

Run `php artisan vendor:publish --tag=laravel-mail` to restyle the layout.

### SEO & sitemap

- Title, description, locale — `config/seo.php`
- Meta tags — `resources/views/partials/seo-meta.blade.php`
- Sitemap routes — `app/Http/Controllers/Seo/SitemapController.php` (lists `/`, `/privacy`, `/terms` by default)

### Nightwatch

Publish the config to tune sampling or redact PII:

```bash
php artisan vendor:publish --tag=nightwatch-config
```

Set `NIGHTWATCH_TOKEN` in `.env` for production ingest.

## Pricing

Free until your project earns. Three tiers, **same code on every tier — only the license changes**:

- **Free** — for projects under $1k/month MRR. MIT-style license up to the cap.
- **Pro** — $29/month or $278/year, up to $10k MRR. Commercial license, continuous updates while subscribed.
- **Studio** — $79/month or $758/year, no revenue cap. Adds white-label rights and unlimited projects.

Buy at [filaas.com/#pricing](https://filaas.com/#pricing).

## License

MIT-style up to your tier's cap, plus a commercial addendum for paid tiers. Plain English in `LICENSE`.
