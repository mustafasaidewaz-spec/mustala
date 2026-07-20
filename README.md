# Mustala — Freelance Web Developer Portfolio

Professional multi-page portfolio for **Mustafa Saide** built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features

- Home, About, Services, Portfolio (+ details), Blog (+ posts/comments), Gallery, Testimonials, Pricing, FAQ, Contact
- Dark / light mode, EN / PT / SW languages, scroll animations, lazy loading, SEO meta tags
- Admin dashboard with CRUD for blog, projects, gallery, testimonials, categories, and contact messages
- Image & video uploads, filters, search, pagination

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP, WAMP, Laragon) or any PHP server with PDO MySQL

## Setup

1. Copy this folder into your web root (e.g. `C:\xampp\htdocs\Mustala`).
2. Create the database:
   - Open phpMyAdmin
   - Import `database/schema.sql`
3. Edit database credentials in `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mustala_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. If the site runs in a subfolder, set:

```php
define('BASE_URL', '/Mustala');
```

5. Open `http://localhost/Mustala/` in your browser (uses `index.php` automatically).

## Admin

- URL: `/admin/login.php`
- Default login: **admin** / **password**
- Change this password after first login (update the `admins` table hash).

## Folder structure

```
Mustala/
├── index.php, about.php, services.php, portfolio.php, project.php
├── blog.php, post.php, gallery.php, testimonials.php
├── pricing.php, faq.php, contact.php
├── assets/css, assets/js, assets/img, assets/uploads
├── includes/   (config, db, functions, lang, header, footer)
├── lang/       (en, pt, sw)
├── admin/      (dashboard + CRUD)
├── api/        (contact + comments)
└── database/schema.sql
```

## Notes

- Without a database connection the public site still loads with sample projects, posts, and testimonials.
- Place your CV at `assets/uploads/Mustafa_Saide_CV.pdf` (or update the path in settings / config).
- Personal photos live in the `img/` folder (e.g. `img/about im.jpeg`) for the About and Teams sections.
- The main entry point is **`index.php`**. Old static HTML files are not used.
