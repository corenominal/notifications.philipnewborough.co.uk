# notifications.philipnewborough.co.uk

A personal notification microservice built with [CodeIgniter 4](https://codeigniter.com/). Acts as a centralised hub for creating, delivering, and managing browser push notifications across a private web ecosystem. Other applications within the ecosystem call its REST API to push notifications to specific users or broadcast them to all users.

## Features

- **Web Push notifications** via VAPID (using [minishlink/web-push](https://github.com/web-push-libs/web-push-php))
- **REST API** for creating and managing notifications from other services
- **Admin dashboard** with DataTables-powered notification management (create, edit, delete, bulk actions)
- **Icon upload** with server-side validation (PNG, square, ≥256×256)
- **Per-user read/cleared state** tracked via join tables — supports broadcast notifications (`user_uuid = 'everyone'`) with independent per-user state
- **Probabilistic GC** — old notifications (30+ days) are pruned automatically
- **SSO integration** — session hydration via an external auth service using cookies and tokens
- **API key auth** — master key for service-to-service calls; user-scoped keys validated against the auth service

## Architecture

```
External Apps
    │  POST /api/notification (master API key)
    ▼
[ApiFilter] ── validates apikey header
    ▼
Api\Notification::insert
    ├── NotificationModel → DB: notifications table
    └── pushNotification()
            ├── SubscriptionModel → DB: subscriptions table
            └── minishlink/WebPush → Browser Push Service → User Browser

Admin Browser (session cookies)
    ▼
[AuthFilter] ── cURL → External Auth Service → session hydration
[AdminFilter] ── checks is_admin session flag
    ▼
Admin\Home / Admin\Create

User Browser
    │  user-scoped apikey + user-uuid header
    ▼
[ApiFilter] ── cURL → auth service keycheck
    ▼
Api\Notifications::index
    └── SQL JOIN: notifications + read + cleared tables
```

## API Endpoints

All API routes require an `apikey` header. `POST /api/notification` requires the master key only.

| Method | Route | Description |
|--------|-------|-------------|
| `GET` | `/api/test/ping` | Health check — returns `{"status":"success","message":"pong"}` |
| `GET` | `/api/notifications/:user_uuid[/:limit]` | Fetch notifications for a user (excludes cleared, includes read flag, default limit 20) |
| `POST` | `/api/notification` | Create notification(s); fires Web Push. `user_uuid` can be a string or array |
| `POST` | `/api/notification/clear` | Mark a notification as cleared for a user |
| `POST` | `/api/notification/clearall` | Clear all notifications for a user |
| `POST` | `/api/notification/read` | Mark a notification as read |
| `POST` | `/api/notification/readall` | Mark all notifications as read for a user |

## Database Tables

- **`notifications`** — notification rows with `uuid`, `title`, `body`, `url`, `icon`, `user_uuid`, `calltoaction`
- **`read`** — records which user has read which notification
- **`cleared`** — records which user has cleared/dismissed which notification
- **`subscriptions`** — Web Push subscription endpoints and keys per user

## Admin Routes

All admin routes require an authenticated session with `is_admin = true`.

| Route | Description |
|-------|-------------|
| `GET /admin` | Dashboard with notifications DataTable |
| `GET /admin/create` | Create notification form |
| `POST /admin/create` | Submit new notification |
| `POST /admin/create/icon-upload` | Upload a PNG notification icon |
| `GET /admin/create/icons` | List uploaded icons |

## Authentication & Authorisation

- **`AuthFilter`** — validates `user_uuid` and `token` cookies against the external auth service via cURL, hydrates session
- **`OptionalAuthFilter`** — same as above but never redirects; silently hydrates session if possible
- **`AdminFilter`** — checks `session('is_admin')`; redirects to `/unauthorised` if false
- **`ApiFilter`** — validates `apikey` header; for user-scoped keys, also requires a `user-uuid` header and validates against the auth service

## Configuration

Key `.env` variables:

```ini
# Application
app.baseURL=

# API Keys
app.masterKey=

# VAPID (Web Push)
app.vapidSubject=
app.vapidPublicKey=
app.vapidPrivateKey=

# Ecosystem URLs
app.tld=
app.auth=
app.assets=
app.sendmail=
app.logs=
app.notifications=
app.tldcookiedomain=
app.cookiedomain=
```

## Requirements

- PHP 8.1+
- MySQL / MariaDB
- Composer
- Node.js / npm (for JS linting)

## Setup

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy and configure environment
cp env .env
# Edit .env with your values

# Run database migrations
php spark migrate
```

## CLI Commands

```bash
# Health check greeting
sudo -u _www php public/index.php cli/test index "Your Name"

# Count demo (1–10 with 1-second intervals)
sudo -u _www php public/index.php cli/test count
```

## Testing

```bash
composer test
```

Tests are in `tests/` and use PHPUnit 10. The suite includes a `HealthTest` that verifies `APPPATH` is defined and `baseURL` is configured.

## Libraries

- **`App\Libraries\Notification`** — fluent builder for sending notifications via this service's own API (for use by other parts of the ecosystem)
- **`App\Libraries\Sendmail`** — fluent email builder that posts to the external sendmail microservice
- **`logit()`** helper — posts structured log messages to the centralised logging service

