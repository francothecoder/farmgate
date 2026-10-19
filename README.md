# FarmGate Zambia V1.1

FarmGate Zambia is a mobile-first agricultural marketplace, buyer request platform, knowledge hub, and business directory designed for Zambia. V1.1 adds seller listing submissions with image uploads, operational moderation queues with decision history, and an editorial CMS for articles and site settings.

## 1. System requirements

- PHP 8.3+
- Composer 2.x
- Node.js 18+
- npm 9+
- SQLite for local development (default) or MySQL 8+
- Web server such as Laravel's built-in `php artisan serve` for development

## 2. Installation instructions

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
```

## 3. Environment configuration

Set up the app environment in `.env`:

```env
APP_NAME="FarmGate Zambia"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmgate
DB_USERNAME=root
DB_PASSWORD=
```

## 4. Database setup

Create the database file for SQLite:

```bash
touch database/database.sqlite
```

Then run:

```bash
php artisan migrate
```

## 5. Migration commands

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan migrate:status
```

## 6. Seeder commands

```bash
php artisan db:seed
php artisan db:seed --class=CountrySeeder
php artisan db:seed --class=SampleContentSeeder
```

## 7. Frontend build commands

```bash
npm install
npm run build
npm run dev
```

## 8. Local development commands

```bash
php artisan serve
php artisan queue:work
php artisan schedule:work
```

## 9. V1.1 operations

- Signed-in, verified sellers can create marketplace listings with up to five JPG, PNG, or WebP photos. New listings enter `pending_review` and are not public until approved.
- Admins can open `/admin` to reach separate queues for listings, buyer requests, business profiles, and reports. Decisions record the acting admin, action, and optional reason in `moderation_events`.
- Admins can manage Knowledge Hub drafts, review submissions, publication state, Markdown body, tags, SEO metadata, featured status, and cover images under `/admin/articles`.
- General site name, tagline, email, and phone settings can be edited under `/admin/settings`; the public layout uses the saved values.
- The homepage carousel automatically features images from approved listings, published articles, and approved businesses. It includes manual navigation, dot controls, keyboard arrows, pause/play, and respects reduced-motion preferences.
- Buyers can contact listing owners on WhatsApp when the owner has provided a valid phone number, send an in-platform enquiry, and follow replies in `/inbox`. Buyers can review offers on their requests and accept or decline them from their inbox.
- The homepage product carousel is curated by administrators from Site settings. Knowledge Hub discussions support threaded replies; moderators approve comments before publication. `php artisan db:seed --class=SampleContentSeeder` adds ten sample products and ten practical guides.
- Seller trust features include admin-granted verified badges, buyer reviews available after an offer is accepted, and report forms for marketplace listings and business profiles. Reports enter the existing admin moderation queue.
- Seller phone numbers can be added at registration or updated from the profile page. WhatsApp contact links are only shown when a usable number is present.
- Local public files use Laravel's `public` disk. Run `php artisan storage:link` after installation; configure an S3-compatible public disk for horizontally scaled production deployments.

## 10. Queue and scheduler configuration

Laravel queues and scheduler are enabled by default with the database driver in this project. For production or cron-based environments, configure a cron entry similar to:

```bash
* * * * * cd /path/to/farmgate && php artisan schedule:run >> /dev/null 2>&1
```

Queue workers should be managed with:

```bash
php artisan queue:work --tries=3
```

## 11. Production deployment checklist

- Configure a real database connection and cache/queue backend.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and a secure `APP_KEY`.
- Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`.
- Use a production-ready web server such as Nginx or Apache.
- Set file permissions for storage and bootstrap/cache.
- Configure mail, object storage, and backups.
- Ensure uploaded images use durable shared object storage in multi-instance deployments.
- Review static asset build and environment variables before deployment.
- Protect admin routes and verify role-based access.

## 12. Test commands

```bash
php artisan test
php artisan test tests/Feature/Auth/RegistrationTest.php
php artisan test tests/Feature/HomePageTest.php
php artisan test tests/Feature/AdminAccessTest.php
php artisan test tests/Feature/AdminWorkflowTest.php
php artisan test tests/Feature/ListingSubmissionTest.php
```

## 13. Known limitations

- Business verification evidence uploads, user-managed listing edits, and category administration are not yet included.
- Full SMS verification and automated content moderation are not included.
- MySQL is supported by the framework, while local development is configured for SQLite to keep setup lightweight and portable.

## Additional notes

- The project includes an implementation checklist in [IMPLEMENTATION_CHECKLIST.md](./IMPLEMENTATION_CHECKLIST.md).
- Sample development data is seeded for a Zambia-specific demo experience.
- The app ships with a role-aware admin route and a user dashboard for authenticated users.

## License

This project is available under the MIT license.
