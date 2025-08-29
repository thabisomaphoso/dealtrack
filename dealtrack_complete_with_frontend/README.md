# DealTrack SA - Complete Starter Project

This package contains a working local starter project with:
- User login/auth (admin role)
- Admin-only pages to add/edit products and suppliers
- Image upload for products
- CSV / Excel importer for price data (uses PhpSpreadsheet if installed)
- Basic frontend to search and compare prices

## Prerequisites
- XAMPP (Apache + MySQL + phpMyAdmin) or equivalent with PHP 7.4+
- Composer (to install PhpSpreadsheet) if you want Excel (.xls/.xlsx) import support:
  ```bash
  cd C:\xampp\htdocs\dealtrack_sa
  composer require phpoffice/phpspreadsheet:^1.29
  ```
  If you already confirmed PhpSpreadsheet works, you're good.

## Setup
1. Extract this folder into `C:\xampp\htdocs\dealtrack_sa`.
2. Start Apache and MySQL in XAMPP.
3. Import SQL schema: open phpMyAdmin -> create database `dealtracksa` -> Import `sql/schema.sql`.
4. Ensure `config.php` DB credentials match (username/password).
5. Visit: `http://localhost/dealtrack_sa/`
6. Login with sample admin account:
   - Username: `admin`
   - Password: `admin123`

## Notes
- Uploaded images and import files are stored in `/uploads/`.
- Excel import requires PhpSpreadsheet (installed via Composer).
- For security, change the sample admin password after first login.

