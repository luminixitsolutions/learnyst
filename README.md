# StudyNest LMS Platform

A complete Laravel 11 + MySQL Learning Management System with Admin, Instructor, and Learner panels.

## Requirements

- PHP 8.2+
- MySQL 8+
- Composer
- Laragon (recommended) or any PHP server

## Installation

1. **Create database in phpMyAdmin:**
   - Open phpMyAdmin → Create database `StudyNest` (utf8mb4_unicode_ci)
   - Or run: `database/StudyNest.sql`

2. **Configure environment:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=StudyNest
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
   - Laragon: set virtual host to `StudyNest.test` pointing to `/public`
   - Or: `php artisan serve`

## Demo Accounts

| Role       | Email                    | Password  |
|------------|--------------------------|-----------|
| Admin      | admin@studynest.com       | password  |
| Sub Admin  | subadmin@studynest.com    | password  |
| Instructor | instructor@studynest.com  | password  |
| Learner    | learner@studynest.com     | password  |

### Demo students

| Student       | Email                         | Password  |
|---------------|-------------------------------|-----------|
| Priya Sharma  | priya.sharma@studynest.com     | password  |
| Arjun Mehta   | arjun.mehta@studynest.com      | password  |
| Sneha Reddy   | sneha.reddy@studynest.com      | password  |
| Vikram Patel  | vikram.patel@studynest.com     | password  |
| Ananya Iyer   | ananya.iyer@studynest.com      | password  |

### Demo institutes (company admin login)

| Institute              | Email                     | Password  | Public page |
|------------------------|---------------------------|-----------|-------------|
| Luminix IT Solution    | luminix@studynest.com      | password  | `/companies/luminix-it-solution` |
| Nova Skills Academy    | nova@studynest.com         | password  | `/companies/nova-skills-academy` |
| Apex Career Institute  | apex@studynest.com         | password  | `/companies/apex-career-institute` |
| BrightPath Learning    | brightpath@studynest.com   | password  | `/companies/brightpath-learning` |
| SkillForge Academy     | skillforge@studynest.com   | password  | `/companies/skillforge-academy` |

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
