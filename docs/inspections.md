# Vehicle Inspection Module

## Overview
This module supports:
- Company-performed inspections in backoffice.
- Driver-performed periodic inspections in mobile app.

## Backend Setup
1. Run migrations:
```bash
php artisan migrate
```

2. Seed permissions:
```bash
php artisan db:seed --class=InspectionPermissionsSeeder
```

3. Configure env:
- `INSPECTION_MAX_PHOTO_KB`
- `INSPECTION_UPLOAD_RATE_LIMIT`
- `FCM_SERVER_KEY`

4. Ensure private storage path exists:
- `storage/app/private/inspections`

## Scheduler and Queue
Run scheduler and queue worker in production:
```bash
php artisan schedule:work
php artisan queue:work --queue=default
```

Scheduled command:
- `inspections:generate-assignments` (hourly)

## API Highlights
Driver:
- `GET /api/driver/inspections/next`
- `POST /api/driver/inspections/{assignment}/start`
- `POST /api/driver/inspections/{assignment}/photo`
- `POST /api/driver/inspections/{assignment}/submit`

Company:
- `GET /api/company/inspections/dashboard`
- CRUD templates/schedules
- assignments list and review

Common:
- `POST /api/device-tokens/register`
- `GET /api/notifications`

## Backoffice Screens
- `/admin/inspections`
- `/admin/inspections/templates`
- `/admin/inspections/schedules`
- `/admin/inspections/assignments`

## Mobile Flow
- Route: `/inspection`
- Guided photo capture by required angles
- Offline queue for photo uploads with retry/sync on reconnection
- Damage reporting and submit
