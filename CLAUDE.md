# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 + React job board application with a multi-guard authentication system supporting three user types:
- **Companies**: Can create and manage job listings
- **Applicants**: Can view and apply for jobs
- **Admins**: Administrative access

The application uses Inertia.js for seamless Laravel-React integration with TypeScript and Tailwind CSS.

## Development Commands

### Starting Development
```bash
# Full development environment (recommended)
composer dev  # Starts Laravel server, queue worker, logs, and Vite dev server concurrently

# Individual services
php artisan serve        # Laravel development server
npm run dev             # Vite development server
php artisan queue:listen # Queue worker
php artisan pail        # Real-time logs
```

### Building & Production
```bash
npm run build          # Build frontend assets
npm run build:ssr      # Build with SSR support
composer dev:ssr       # Run with SSR in development
```

### Testing
```bash
php artisan test                    # Run all tests (uses Pest)
php artisan test --filter=Feature  # Run feature tests only
php artisan test --filter=Unit     # Run unit tests only
composer test:type-coverage        # Run tests with type coverage (min 100%)
```

### Code Quality
```bash
# Linting and formatting
composer lint          # Run both PHP and JavaScript linters
pint                  # PHP code style fixer (Laravel Pint)
npm run lint          # ESLint with auto-fix
npm run format        # Prettier formatting
npm run format:check  # Check formatting without fixing

# Static analysis and refactoring
composer test:types    # PHPStan static analysis
phpstan               # Direct PHPStan run
composer refactor     # Rector refactoring
composer test:refactor # Rector dry-run
npm run types         # TypeScript type checking
```

## Architecture & Key Patterns

### Multi-Guard Authentication
The application uses Laravel's multi-guard system:
- `web` guard: Default users (admins)
- `applicant` guard: Job seekers
- `company` guard: Job posting companies

Guards are defined in `config/auth.php` with custom providers and guards in `app/Guards/` and `app/Auth/Providers/`.

### Frontend Architecture
- **Framework**: React 19 with TypeScript
- **Routing**: Inertia.js for SPA-like experience
- **UI Components**: Radix UI primitives with custom components in `resources/js/components/ui/`
- **Styling**: Tailwind CSS v4 with custom design system
- **State Management**: React hooks with appearance persistence

### Job Listing System
Core models and relationships:
- `JobListing` model with comprehensive attributes (salary, location, employment type, etc.)
- Swiss-specific location support (cantons, regions, sub-regions)
- Skills and categories system with many-to-many relationships
- Application process management with screening questions

### Backend Patterns
- **Actions**: Business logic encapsulated in dedicated Action classes (`app/Actions/`)
- **Enums**: PHP 8.1+ enums for type safety (`app/Enums/`)
- **Form Requests**: Validation logic in dedicated request classes
- **Policies**: Authorization via Laravel policies (`app/Policies/`)
- **Strict Types**: All PHP files use `declare(strict_types=1)`

## File Structure Highlights

### Key Directories
- `app/Actions/`: Business logic actions (Create, Update, Delete operations)
- `app/Enums/`: Type-safe enums for employment types, job categories, etc.
- `app/Guards/`: Custom authentication guards
- `resources/js/components/`: React components (UI kit in `ui/` subdirectory)
- `resources/js/pages/`: Page components organized by user type
- `resources/js/types/`: TypeScript type definitions
- `routes/`: Route files organized by feature (`job_listings.php`, `company_auth.php`, etc.)

### Notable Features
- Comprehensive test coverage with Pest PHP
- Multi-language support (English/German)
- Swiss location system with cantons and regions
- Job application screening questions
- Company onboarding flow
- Responsive design with mobile-first approach

## Database
- SQLite for development (`database/database.sqlite`)
- Comprehensive migration system with Swiss geographical data
- Factory classes for all models in `database/factories/`
- Seeder classes for initial data

## Code Quality Tools
- **PHPStan**: Level 8 static analysis with Larastan
- **Pint**: Laravel's PHP code style fixer
- **Rector**: PHP refactoring tool
- **ESLint**: JavaScript/TypeScript linting
- **Prettier**: Code formatting
- **TypeScript**: Strict type checking