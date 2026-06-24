# GCTU Online Project Library

## Overview

This repository is a PHP-based digital library for Ghana Communication Technology University student projects, dissertations, and research papers.

## Architecture

- Core entrypoints delegate to a lightweight MVC layer under `app/` while still supporting legacy-style page files.
- Shared functionality lives in `includes/bootstrap.php`, `includes/functions.php`, `includes/auth.php`, and `includes/csrf.php`.
- `app/controllers/` contains the main page controllers, `app/models/` contains database helpers, and `app/views/` contains page templates.
- Layout components remain in `includes/header.php` and `includes/footer.php`.

## Recent Fixes

- Admin login redirect and approval flow now use root-relative paths, avoiding nested `/admin/admin/...` loops.
- PDF upload validation now degrades gracefully when the `fileinfo` extension is unavailable:
  - uses `finfo_open()` when present
  - falls back to `mime_content_type()` when available
  - inspects the first bytes of the file if needed
- Uploads are stored under `storage/uploads` and served only through secure download logic.
- Admin dashboard approve/reject actions now redirect correctly to `/admin/dashboard.php`.

## Security Improvements

- Secure session cookie settings:
  - `session.use_strict_mode = 1`
  - `httponly` and `SameSite=Lax`
  - `secure` when HTTPS is available
- Form CSRF protection through `includes/csrf.php`.
- Upload validation includes extension and MIME checks.
- File downloads are served only through the allowed upload directory and controlled download flow.
- Admin access is protected by role-based checks using shared auth helpers.
- Basic HTTP security headers are sent on every request.
- Uploaded files are kept outside the web root in `storage/uploads/`.

## Setup

1. Copy `config/env.php` and update database credentials if needed.
2. Ensure `storage/uploads/` is writable by the web server and exists.
3. Import the schema from `config/schema.sql` and seed data if desired.
4. Start your PHP server in the project root.
5. Open the application in your browser.

## Usage

- Register or log in as a user before uploading a PDF.
- Approved projects are visible in `browse.php`, `search.php`, and project detail pages.
- Admin users may sign in and review pending uploads from `/admin/dashboard.php`.
- The seed database includes demo credentials:
  - `admin / admin123`
  - `student / student123`

## Notes

- `upload.php` and `download.php` are protected by authentication.
- Legacy page entrypoints remain thin wrappers that bootstrap the shared app layer.
- `storage/uploads/.htaccess` is included to block direct access on Apache.
