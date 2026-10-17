# Centralized Authentication & POS Gateway Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform PATEN from isolated 64-character capability tokens and obscure UUID links into a unified, user-friendly system featuring Email/Password authentication for Event Organizers & Dinas, an "Event Saya" central dashboard, and a centralized mobile-first POS gateway for booth merchants using 6-digit PINs.

**Architecture:** 
1. Standard Laravel Session Authentication (Breeze-pattern) with `users` table extended with `role` (`admin_dinas`, `organizer`).
2. Event ownership link (`events.user_id`) linking events to their organizers.
3. Dual-mode CMS authorization in `VerifyCmsTokenMiddleware`: grants access if the user is authenticated (`auth()->check()`) OR provides a valid secret token (preserving 100% backward compatibility with all 73 existing feature tests).
4. Central Organizer Dashboard (`/dashboard`) listing all created events with direct 1-click action buttons (CMS, Public Portal, Leaderboard, SPK Evaluations).
5. Centralized POS Gateway (`/pos`) allowing merchants to select their event, choose their stand number, type their 6-digit PIN, and enter their cashier terminal immediately.

**Tech Stack:** Laravel 13, Inertia v3, Vue 3, Tailwind CSS v4, Hallmark Triad Design System (Deep Navy `#132952`, Emerald Green `#00B262`, Warm Amber `#FAA21B`).

---

## Global Constraints
- Preserve Hallmark Triad color palette: Deep Navy `#132952`, Vibrant Emerald `#00B262`, Warm Amber `#FAA21B`.
- Zero disruption to all 73 existing feature and unit tests (backward compatibility for token access preserved).
- Stand kasir remains numeric 6-digit PIN on tactile touch numpad for maximum speed at outdoor stalls.
- All modified PHP code must pass Pint styling: `vendor/bin/pint --dirty --format agent`.
- Frontend bundles must build cleanly with Vite: `npm run build`.

---

## Proposed File Structure

### New Files:
- Migration: `database/migrations/2026_10_17_000002_add_user_id_to_events_and_role_to_users.php`
- Controller: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Controller: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Controller: `app/Http/Controllers/DashboardController.php`
- Controller: `app/Http/Controllers/PosGatewayController.php`
- Form Request: `app/Http/Requests/Auth/LoginRequest.php`
- Vue Page: `resources/js/Pages/Auth/Login.vue`
- Vue Page: `resources/js/Pages/Auth/Register.vue`
- Vue Page: `resources/js/Pages/Organizer/Dashboard.vue`
- Vue Page: `resources/js/Pages/Pos/Gateway.vue`
- Feature Test: `tests/Feature/CentralizedAuthAndGatewayTest.php`

### Modified Files:
- `app/Models/User.php`: add `role` fillable and `events()` relationship.
- `app/Models/Event.php`: add `user_id` fillable and `user()` relationship.
- `app/Http/Middleware/VerifyCmsTokenMiddleware.php`: allow authenticated user or valid token.
- `app/Http/Controllers/EventController.php`: associate `auth()->id()` on `store()`.
- `routes/web.php`: register auth, dashboard, and central `/pos` routes.
- `resources/js/Pages/Welcome.vue`: add header login / dashboard links and prominent "Gerbang Kasir POS" link.
- `resources/js/Layouts/PublicLayout.vue`: show user profile / logout button if authenticated.

---

## Task Decomposition

### Task 1: Database Migration & Model Relationships

**Files:**
- Create: `database/migrations/2026_10_17_000002_add_user_id_to_events_and_role_to_users.php`
- Modify: `app/Models/User.php`
- Modify: `app/Models/Event.php`
- Test: `tests/Feature/CentralizedAuthAndGatewayTest.php`

- [ ] **Step 1: Write the failing feature test**
Create `tests/Feature/CentralizedAuthAndGatewayTest.php` verifying User has role, Event belongs to User, and User has events.

- [ ] **Step 2: Run test to verify it fails**
Run: `vendor/bin/phpunit tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: FAIL (column or relationship missing).

- [ ] **Step 3: Create migration and update models**
Create `database/migrations/2026_10_17_000002_add_user_id_to_events_and_role_to_users.php` adding `role` to `users` and `user_id` (nullable foreign key) to `events`.
Run `php artisan migrate`.
Update `User.php` and `Event.php` with fillable and relationship methods.

- [ ] **Step 4: Run test to verify it passes**
Run: `vendor/bin/phpunit tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: PASS.

---

### Task 2: Authentication Controllers, Form Request & Routes

**Files:**
- Create: `app/Http/Requests/Auth/LoginRequest.php`
- Create: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Create: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/CentralizedAuthAndGatewayTest.php`

- [ ] **Step 1: Add login, registration, and logout test methods**
Add tests to `CentralizedAuthAndGatewayTest.php`:
- `test_login_screen_can_be_rendered()`
- `test_users_can_authenticate_using_the_login_screen()`
- `test_users_can_not_authenticate_with_invalid_password()`
- `test_registration_screen_can_be_rendered()`
- `test_new_users_can_register()`
- `test_users_can_logout()`

- [ ] **Step 2: Run tests to verify failure**
Run: `vendor/bin/phpunit --filter=test_users_can_authenticate tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: FAIL (routes/controllers missing).

- [ ] **Step 3: Implement LoginRequest and Auth Controllers**
Implement `LoginRequest` with rate limiting, `AuthenticatedSessionController` (`create`, `store`, `destroy`), and `RegisteredUserController` (`create`, `store`). Register routes in `routes/web.php`.

- [ ] **Step 4: Run tests to verify pass**
Run: `vendor/bin/phpunit --filter=Auth tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: PASS.

---

### Task 3: Inertia Auth Pages (Login & Register)

**Files:**
- Create: `resources/js/Pages/Auth/Login.vue`
- Create: `resources/js/Pages/Auth/Register.vue`

- [ ] **Step 1: Build `resources/js/Pages/Auth/Login.vue`**
Implement sleek Hallmark Triad login card with:
- BrandMark and subtitle: "Sistem Pengelolaan Event & Kurasi Bantuan UMKM Kutim"
- Input: Email & Password
- Remember me checkbox
- Link to Register ("Belum punya akun panitia? Daftar")
- Back to portal link ("← Kembali ke Beranda")

- [ ] **Step 2: Build `resources/js/Pages/Auth/Register.vue`**
Implement registration card with Name, Email, Password, Password Confirmation, and Role selection (`organizer` / `admin_dinas`).

- [ ] **Step 3: Verify frontend compilation**
Run: `npm run build`
Expected: Build succeeds with 0 errors.

---

### Task 4: Organizer Central Dashboard ("Event Saya") & Dual-Mode CMS Middleware

**Files:**
- Create: `app/Http/Controllers/DashboardController.php`
- Create: `resources/js/Pages/Organizer/Dashboard.vue`
- Modify: `app/Http/Middleware/VerifyCmsTokenMiddleware.php`
- Modify: `app/Http/Controllers/EventController.php`
- Test: `tests/Feature/CentralizedAuthAndGatewayTest.php`

- [ ] **Step 1: Add dashboard and dual-mode authorization tests**
Add tests:
- `test_authenticated_organizer_can_view_dashboard_with_their_events()`
- `test_authenticated_organizer_can_access_their_cms_without_token()`
- `test_organizer_creating_event_is_assigned_as_owner()`

- [ ] **Step 2: Run test to verify failure**
Run: `vendor/bin/phpunit --filter=test_authenticated_organizer tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement DashboardController & update VerifyCmsTokenMiddleware**
In `VerifyCmsTokenMiddleware.php`:
If user is authenticated (`auth()->check()`) AND (user is `admin_dinas` OR `event->user_id === auth()->id()`), allow access and attach event!
If not authenticated or accessing someone else's event, fall back to token verification (query, header, session).
In `EventController::store`, assign `'user_id' => auth()->id()`.
Implement `DashboardController::index` returning Inertia `Organizer/Dashboard` with metrics and events.

- [ ] **Step 4: Build `Organizer/Dashboard.vue`**
Create clean dashboard view with:
- Hero banner with user name & role badge
- KPI summary: Total Event, Total Stand, Total Transaksi, Total Omzet
- Button `+ Buat Event Baru`
- Grid of Event Cards with 1-click action buttons:
  - `Kelola CMS ⚙️` (`/cms/{slug}`)
  - `Portal Publik ↗` (`/e/{slug}`)
  - `Leaderboard 🏆` (`/e/{slug}/leaderboard`)
  - `Evaluasi Hibah SPK 📊` (`/cms/{slug}/evaluations`)

- [ ] **Step 5: Run tests to verify pass**
Run: `vendor/bin/phpunit --filter=Dashboard tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: PASS.

---

### Task 5: Centralized POS Gateway (`/pos`) for Merchants

**Files:**
- Create: `app/Http/Controllers/PosGatewayController.php`
- Create: `resources/js/Pages/Pos/Gateway.vue`
- Modify: `routes/web.php`
- Test: `tests/Feature/CentralizedAuthAndGatewayTest.php`

- [ ] **Step 1: Add POS gateway test**
Add tests:
- `test_pos_gateway_renders_event_and_stand_options()`
- `test_pos_gateway_authenticates_with_valid_pin_and_redirects_to_terminal()`
- `test_pos_gateway_rejects_invalid_pin()`

- [ ] **Step 2: Run test to verify failure**
Run: `vendor/bin/phpunit --filter=test_pos_gateway tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement PosGatewayController and routes**
- `GET /pos`: renders `Pos/Gateway` with list of active events and their stands (only public stand info).
- `POST /pos/authenticate`: validates `stand_id` & `pin_pos`. If valid, puts `pos_stand_id` into session and redirects to `/pos/{stand_id}`.

- [ ] **Step 4: Build `Pos/Gateway.vue`**
Touch-friendly mobile screen:
- BrandMark header with "Gerbang Kasir Stand Bazar" badge.
- Step 1: Select Event Bazar (dropdown / search).
- Step 2: Select Stand (Nomor Stand / Nama Usaha).
- Step 3: Enter 6-digit PIN on tactile keypad.
- Submit CTA: "Buka Terminal Kasir Stand →".

- [ ] **Step 5: Run tests to verify pass**
Run: `vendor/bin/phpunit --filter=pos_gateway tests/Feature/CentralizedAuthAndGatewayTest.php`
Expected: PASS.

---

### Task 6: Navigation Integration & Full Regression Verification

**Files:**
- Modify: `resources/js/Pages/Welcome.vue`
- Modify: `resources/js/Layouts/PublicLayout.vue`
- Modify: `resources/js/Components/Navigation/GlobalCommandPalette.vue`
- Run: Full test suite (`php artisan test`)

- [ ] **Step 1: Update Top Navigation in `PublicLayout.vue` & `Welcome.vue`**
Add:
- Link to `/pos` ("Kasir Stand 📱")
- If guest: "Masuk (Login)" button linking to `/login`
- If authenticated: "Dasbor Saya" button linking to `/dashboard` and "Keluar" button.

- [ ] **Step 2: Run Pint and Vite Build**
Run: `vendor/bin/pint --dirty --format agent`
Run: `npm run build`

- [ ] **Step 3: Run Full Test Suite**
Run: `php artisan test --compact`
Expected: ALL tests PASS (including all existing 73 tests + new auth & gateway tests).
