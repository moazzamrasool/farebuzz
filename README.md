<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Laravel Multi-Auth System

## Features

### User Dashboard
Separate user dashboard with dedicated functionality.

### Admin Dashboard
Admin-specific dashboard for complete control over the system.

### Multiple Authentication Guards
Different guards for users and admins to ensure secure authentication.

### User Registration
Seamless registration process for users with validation and security.

### Admin Login
Focused on secure login process for admins, bypassing registration.

### Middleware Integration
Custom middleware for guest access, authenticated routes, and role-based access control.

### Secure Authentication Mechanisms
Utilizes Laravel’s built-in authentication with enhanced security features.

## Requirements
- Laravel 11
- PHP 8.1
- Composer 2.x

## Getting Started
- Clone the repository.
- Run composer install to install dependencies.
- Set up your database configuration in the .env file.
- Run php artisan migrate to set up the necessary tables.
- Implement and customize as needed.

```javascript
composer install
```

```javascript
php artisan migrate
```

```javascript
php artisan serve
```

## CRM: Leads / Follow-up Reminders — background processes required

The Leads module (`/crm/package-enquiries`) schedules follow-up reminder emails
(`App\Jobs\SendFollowUpReminderJob`, dispatched from the `Schedule::call(...)` entry in
`routes/console.php`). Two background processes must be running for reminders to
actually fire — without them, follow-ups will show as due/overdue in the CRM but no
email will ever be sent:

1. **The scheduler** — checks every few minutes for follow-ups that are now due and
   queues a reminder job for each one (guarded by a `reminder_sent` flag, so each
   follow-up only ever fires one reminder).

   ```bash
   # Local development — keeps running in the foreground, ticks every minute:
   php artisan schedule:work
   ```

   ```cron
   # Production — add ONE line to the server's crontab (not php artisan schedule:work):
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **The queue worker** — actually sends the queued reminder emails
   (`QUEUE_CONNECTION=database` in `.env`, backed by the `jobs` table).

   ```bash
   # Local development:
   php artisan queue:work database

   # Production — run under a process manager so it restarts on crash/deploy
   # (e.g. Supervisor). Example supervisor program block:
   #   command=php /path-to-project/artisan queue:work database --sleep=3 --tries=3
   #   autostart=true
   #   autorestart=true
   #   numprocs=1
   ```

   After every deploy that changes queued job code, restart the worker so it picks up
   the new code:

   ```bash
   php artisan queue:restart
   ```

Without both processes running, everything else in the Leads module (status changes,
notes, timeline, WhatsApp click-to-chat, manually-sent emails, follow-up scheduling
itself) still works normally — only the *automated* reminder email depends on these.

## CRM: WhatsApp Bot (Cloud API) — local webhook setup

The AI lead-collection bot (`App\Http\Controllers\WhatsAppWebhookController`,
`App\Services\WhatsApp\*`) receives inbound WhatsApp messages via a webhook that Meta
calls directly — `/webhook/whatsapp`. Meta can only reach that URL if it's publicly
addressable, so local development needs a tunnel (ngrok) in front of XAMPP's Apache.

### 1. One-time setup

- **Per-tenant credentials**: each company enters its own WhatsApp Business Cloud API
  `access_token`, `phone_number_id`, and `business_account_id` on the CRM's own settings
  page — `crm/whatsapp-settings` (company-owner tier only) — not in `.env`.
- **Shared app secrets** (one Meta Developer App serves every tenant), in `.env`:

  ```env
  WHATSAPP_VERIFY_TOKEN=   # any string you invent — must match what you paste into Meta's webhook config
  WHATSAPP_APP_SECRET=     # Meta app dashboard → App settings → Basic → App secret
  WHATSAPP_BOT_RATE_LIMIT_PER_HOUR=30
  ```

### 2. Expose your local webhook with ngrok

```bash
# Install once (Windows):
winget install --id Ngrok.Ngrok -e

# Authenticate once — get your token from https://dashboard.ngrok.com/get-started/your-authtoken
ngrok config add-authtoken <your-authtoken>

# Start a tunnel to XAMPP's Apache (port 80) — leave this running while you test
ngrok http 80
```

ngrok prints a public HTTPS URL (e.g. `https://xxxx.ngrok-free.app`). Your webhook is then
reachable at:

```
https://xxxx.ngrok-free.app/farebuzz-admin/public/webhook/whatsapp
```

**The free ngrok URL changes every time the tunnel restarts** — re-paste it into Meta's
webhook config (step 3) after every restart.

### 3. Configure the webhook in the Meta Developer dashboard

1. App → **WhatsApp** → **Configuration** → **Webhook** → **Edit**.
2. **Callback URL**: the ngrok URL from step 2 above.
3. **Verify token**: the exact value of `WHATSAPP_VERIFY_TOKEN` from `.env`.
4. **Verify and save** — Meta calls the URL with a GET request; a matching token gets an
   instant success.
5. **Webhook fields** → **Manage** → subscribe to **messages**.
6. Confirm the **Phone Number ID** shown on Meta's API Setup page matches what's saved on
   the CRM's `crm/whatsapp-settings` page for that tenant, and that **Bot enabled** is checked.

Send a WhatsApp message to the test/business number from your phone to trigger the bot.

### 4. Going to production

- Swap the test number for a verified business number and complete Meta Business
  Verification.
- Move the per-tenant `access_token` to a permanent, non-expiring token (Meta app dashboard
  → System User → generate token).
- Replace the ngrok tunnel with your real, permanently-reachable `APP_URL` and re-point
  Meta's webhook Callback URL at it once.

