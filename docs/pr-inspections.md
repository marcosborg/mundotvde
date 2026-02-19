# PR Description - Vehicle Inspection Upgrade

## Found architecture summary
- Backend: Laravel 9 with Sanctum (`auth:sanctum` in API routes).
- Authorization: role/permission via `roles`, `permissions`, and dynamic gates (`AuthGates` middleware).
- Driver/vehicle linkage: `drivers.user_id` and `vehicle_items.driver_id`.
- Mobile: Ionic Angular (standalone components) + Capacitor 7.
- Mobile API client: centralized in `src/app/services/api.service.ts` with token persisted in Capacitor Preferences.
- Existing app routes and tabs were preserved; new module added as separate route.

## What is included
- Inspection data model (templates, schedules, assignments, submissions, photos, defects, device tokens, settings).
- Driver inspection APIs (next/start/photo/delete/submit) with ownership checks.
- Company APIs for templates, schedules, assignments, review workflow.
- Private photo storage + signed download endpoints.
- Scheduled assignment generation and overdue/reminder queue dispatch.
- Push registration endpoint and FCM service wrapper.
- Backoffice web module under `/admin/inspections`:
  - dashboard
  - templates
  - schedules
  - assignments list/manual creation
  - assignment detail, review, evidence ZIP download
- Mobile inspection module under `/inspection`:
  - guided angle capture
  - damage reporting
  - offline upload queue with sync on reconnect
  - due-inspection banner on home tab

## Validation
- Laravel syntax checks passed on new/changed controllers/commands/routes.
- Feature tests added and passing:
  - scheduler generation/overdue
  - driver permission protection
  - upload validation
  - required angles enforcement
- Ionic build passed (`npm run build`).

## Ops/config notes
- New `.env.example` keys:
  - `INSPECTION_MAX_PHOTO_KB`
  - `INSPECTION_UPLOAD_RATE_LIMIT`
  - `FCM_SERVER_KEY`
- Required runtime processes:
  - scheduler (`schedule:work` / cron)
  - queue worker (`queue:work`)

## Docs
- `docs/inspections.md` setup and flow docs.
- `docs/CHANGELOG.md` concise entry.
