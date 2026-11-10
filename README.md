# DD Cargo — Logistics Operations & Shipment Management

**A custom Laravel platform for international parcel operations, warehouse workflows, courier tasks, and shipping documentation.**

This repository is a source-code portfolio snapshot of a multi-module logistics application. It includes both an administrative web system and API endpoints for related mobile/courier workflows.

## Project overview

The system brings together processes that would otherwise be spread across spreadsheets, paper forms, and separate internal tools: shipment intake, tracking, packing, warehouse handling, courier assignments, receipts, and printable documents.

### Core features

| Area | Functionality represented in the codebase |
| --- | --- |
| **Parcel intake & tracking** | Customer-facing shipment forms, tracking lookups, and tracking by phone |
| **Operations dashboard** | Shipment worksheets, statuses, filters, administrative search, and archives |
| **Warehouse & packing** | Packing lists, pallet data, invoices, manifests, and shipment grouping |
| **Courier management** | Courier task lists, status updates, and tracking assignment |
| **Document workflows** | Signed forms, PDF generation, download/export, and receipt handling |
| **Bulk data** | Spreadsheet/CSV import and export for tracking data, checklists, and operational reports |
| **Access management** | Login-protected administration, users, and role-related modules |
| **Mobile API** | Endpoints for authentication, task retrieval, scanning workflows, receipts, and packing operations |
| **International workflows** | Different parcel/worksheet flows and localized interfaces |

## What makes it interesting technically

- **Connected operational entities:** worksheets, tracking numbers, packing lists, manifests, receipts, courier tasks, and archives are handled as related parts of a workflow.
- **Document generation:** Laravel-based PDF and Excel export capabilities support printable logistics forms and data exchange.
- **Web + API architecture:** browser-based operations and mobile clients work with the same server-side business domain.
- **Multiple workflows:** the application supports different shipment types and regional/partner-specific forms rather than assuming a single shipment process.
- **Operational history:** tracking, checklists, archives, and status changes are represented in dedicated modules.

## Technology

| Layer | Stack |
| --- | --- |
| Server | PHP, **Laravel 5.6** |
| UI | Laravel Blade, JavaScript, CSS, Laravel Mix |
| Data | Relational database via Eloquent |
| Documents | Dompdf / Laravel PDF libraries, Laravel Excel |
| Integrations | REST-style JSON endpoints, shipment tracking and external service integrations |
| Tooling | Composer, npm/Yarn, Artisan, PHPUnit configuration |

This is a **legacy Laravel codebase**: its dependencies and runtime requirements reflect the original project, not current Laravel releases.

## Repository structure

```text
app/
  Http/Controllers/
    Admin/           # Worksheets, couriers, packing, archive, warehouse
    API/             # Endpoints consumed by external/mobile clients
  Console/           # Artisan commands
  Exports/           # Spreadsheet exports
  Imports/           # Data imports
  *.php              # Shipment, tracking, packing and receipt models
resources/views/
  admin/             # Operations dashboard
  pdf/               # Printable documents
  *.blade.php        # Parcel and tracking forms
routes/
  web.php            # Web and admin workflows
  api.php            # JSON API endpoints
database/            # Database schema and seeds
```

## Running a local development copy

The project requires an environment compatible with its **Laravel 5.6** dependency tree and the PHP extensions declared in `composer.json` (including GD and ZIP). It may not install unchanged on current PHP/Node versions.

```bash
git clone https://github.com/filimonenkovyacheslav/dd_cargo_git.git
cd dd_cargo_git

composer install
cp .env.example .env
php artisan key:generate

# Configure a local database and any required external-service credentials.
php artisan migrate
npm install
npm run dev
php artisan serve
```

These are **setup steps, not a verified one-command deployment**. A usable installation also needs the expected project data, compatible dependency versions, and integration configuration. Use a local, isolated database rather than connecting a test copy to a production environment.

## Related mobile workflow

The separate [Union Logistics React Native application](https://github.com/filimonenkovyacheslav/UnionLogisticiOS) contains courier-oriented screens and API calls corresponding to logistics features represented here.

## Portfolio note

This repository is shared to demonstrate the application structure and implementation approach—not as a maintained, hosted shipping service or a production-ready starter kit.

**Security:** treat this historical public snapshot as untrusted for deployment. Audit embedded configuration and credentials, authentication, endpoint authorization, old dependencies, and uploaded files before running it publicly. Do not reuse historic API tokens or production data.

## Developer

**[Vyacheslav Filimonenko](https://github.com/filimonenkovyacheslav)** — Full-Stack Developer working with PHP/Laravel, APIs, business process automation, and mobile/web integrations.
