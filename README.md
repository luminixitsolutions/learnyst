# Learnyst LMS Platform

A complete Laravel 11 + MySQL Learning Management System with Admin, Instructor, and Learner panels.

## Requirements

- PHP 8.2+
- MySQL 8+
- Composer
- Laragon (recommended) or any PHP server

## Installation

1. **Create database in phpMyAdmin:**
   - Open phpMyAdmin → Create database `learnyst` (utf8mb4_unicode_ci)
   - Or run: `database/learnyst.sql`

2. **Configure environment:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=learnyst
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Install & migrate:**
   ```bash
   composer install --no-security-blocking
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

4. **Run the app:**
   - Laragon: set virtual host to `learnyst.test` pointing to `/public`
   - Or: `php artisan serve`

## Demo Accounts

| Role       | Email                    | Password  |
|------------|--------------------------|-----------|
| Admin      | admin@learnyst.com       | password  |
| Sub Admin  | subadmin@learnyst.com    | password  |
| Instructor | instructor@learnyst.com  | password  |
| Learner    | learner@learnyst.com     | password  |

## Modules

- Authentication (role-based login, profile, password reset)
- Dashboard with sales charts & analytics
- Products/Courses (curriculum, lessons, drip, duplicate)
- Learners (CRUD, enroll, import/export CSV)
- Sales/Orders (manual orders, invoices, refunds)
- Payments (Razorpay-ready structure, manual payments)
- Batches, Instructors, Communities, Discussions
- **Bundles** (multi-course packs), **Groups** (learner segments)
- **Enrollments** (course/batch/bundle with access dates)
- **Sub-Admins** (7-step wizard, roles & granular permissions)
- **Checkout Consents** (required at order, acceptance report)
- **Social Links** (dynamic footer/settings)
- Certificates (templates, issue, verify)
- Marketing (coupons, campaigns, leads)
- Free Resources, Categories, Segments
- Reports & Settings

## Design

Premium dark theme with emerald accent — distinct modern UI (not copied from reference).

## Security

- CSRF protection, role middleware, activity logs, file upload validation
