# Spare Xpress Deployment Guide

This guide deploys local changes to the VPS without disturbing the other hosted sites.

## Production Server

- VPS: `185.2.101.46`
- SSH user: `root`
- Project path: `/var/www/spare-xpress`
- Public domain: `https://sparexpressltd.com`
- Admin URL: `https://sparexpressltd.com/admin/`
- PHP-FPM socket: `/run/php/php8.3-fpm.sock`
- Database: MariaDB database `sparexpress_db`

## Important Rules

- Do not commit `.env`.
- Keep local `.env` for local testing only.
- Keep production `.env` only on the VPS.
- Do not edit the other Nginx sites: `jubatown-admin`, `jubatown-store`, `nhonest`.
- After changing dependencies, always run `composer install` on the VPS.

## 1. Deploy Code Changes

From local PowerShell:

```powershell
git status
git add .
git commit -m "Describe your change"
git push origin main
```

On the VPS:

```bash
ssh root@185.2.101.46
cd /var/www/spare-xpress
git pull origin main
composer install --no-dev --optimize-autoloader
chown -R www-data:www-data /var/www/spare-xpress
chmod -R 775 /var/www/spare-xpress/uploads
systemctl reload php8.3-fpm
nginx -t
systemctl reload nginx
curl -I https://sparexpressltd.com
```

Expected result:

```text
HTTP/2 200
```

## 2. Production `.env`

Edit production env:

```bash
nano /var/www/spare-xpress/.env
```

Recommended production values:

```env
SITE_NAME="SPARE XPRESS LTD"
SITE_URL="https://sparexpressltd.com"
SITE_EMAIL="info@sparexpressltd.com"

DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="sparexpress_db"
DB_USER="sparexpress_user"
DB_PASS="PRODUCTION_DB_PASSWORD"

MAIL_PROVIDER="resend"
RESEND_API_KEY="PRODUCTION_RESEND_API_KEY"
SMTP_FROM_EMAIL="info@sparexpressltd.com"
SMTP_FROM_NAME="SPARE XPRESS LTD"
MAIL_ADMIN_COPY="info@sparexpressltd.com"

CLOUDINARY_CLOUD_NAME="PRODUCTION_CLOUD_NAME"
CLOUDINARY_API_KEY="PRODUCTION_CLOUDINARY_KEY"
CLOUDINARY_API_SECRET="PRODUCTION_CLOUDINARY_SECRET"

GOOGLE_CLIENT_ID="PRODUCTION_GOOGLE_CLIENT_ID"
GOOGLE_CLIENT_SECRET="PRODUCTION_GOOGLE_CLIENT_SECRET"
GOOGLE_REDIRECT_URI="https://sparexpressltd.com/pages/google_callback.php"
```

After editing:

```bash
systemctl reload php8.3-fpm
```

## 3. Local `.env`

Local `.env` should point to your local database:

```env
SITE_URL="http://localhost:8000"

DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="sparedb"
DB_USER="root"
DB_PASS=""
```

Start local server:

```powershell
php -S localhost:8000
```

## 4. Deploy Database Changes

Use this only when local database structure/data changed and you want to replace production DB.

Export local database:

```powershell
& "C:\xampp\mysql\bin\mysqldump.exe" -u root sparedb > sparedb_current.sql
```

If local MySQL root has a password:

```powershell
& "C:\xampp\mysql\bin\mysqldump.exe" -u root -p sparedb > sparedb_current.sql
```

Upload SQL to VPS:

```powershell
scp .\sparedb_current.sql root@185.2.101.46:/root/sparedb_current.sql
```

Import on VPS:

```bash
ssh root@185.2.101.46
mariadb -u sparexpress_user -p sparexpress_db < /root/sparedb_current.sql
rm /root/sparedb_current.sql
systemctl reload php8.3-fpm
```

Verify tables:

```bash
mariadb -u sparexpress_user -p sparexpress_db -e "SHOW TABLES;"
```

## 5. Test Email After Deploy

The VPS blocks SMTP, so production email uses Resend API over HTTPS.

```bash
cd /var/www/spare-xpress
php -r "require 'includes/config.php'; require 'includes/email.php'; echo SMTP_FROM_EMAIL.PHP_EOL; echo MAIL_ADMIN_COPY.PHP_EOL; var_dump((new EmailService())->sendTestEmail('your-test-email@example.com','Test User','Spare Xpress test','Email test after deploy'));"
```

Expected:

```text
info@sparexpressltd.com
info@sparexpressltd.com
bool(true)
```

## 6. Useful Checks

Check Nginx:

```bash
nginx -t
systemctl status nginx --no-pager
```

Check PHP-FPM:

```bash
systemctl status php8.3-fpm --no-pager
```

Check site:

```bash
curl -I https://sparexpressltd.com
curl -I https://sparexpressltd.com/admin/
```

Check logs:

```bash
tail -n 100 /var/log/nginx/spare-xpress_error.log
tail -n 100 /var/log/nginx/error.log
```

## 7. If `git pull` Shows Dubious Ownership

Run once:

```bash
git config --global --add safe.directory /var/www/spare-xpress
```

Then:

```bash
cd /var/www/spare-xpress
git pull origin main
```

## 8. After Secrets Are Exposed

Rotate exposed secrets immediately:

- Resend API key
- Cloudinary API secret
- Google OAuth client secret
- Gmail/Zoho app passwords
- Database password if leaked

Then update only:

```bash
nano /var/www/spare-xpress/.env
systemctl reload php8.3-fpm
```
