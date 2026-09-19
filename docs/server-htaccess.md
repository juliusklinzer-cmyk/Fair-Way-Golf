# .htaccess-Ergänzungen für Hetzner Webhosting

Nach dem Duplicator-Import in die `.htaccess` im Webroot einfügen, **oberhalb** des
WordPress-Blocks (`# BEGIN WordPress`). Getestet nach Vorbild firmengolf.app.

```apache
# Kompression
<IfModule mod_brotli.c>
  AddOutputFilterByType BROTLI_COMPRESS text/html text/css text/javascript application/javascript application/json image/svg+xml font/woff2
</IfModule>
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json image/svg+xml
</IfModule>

# Cache-Header: Assets tragen eine Version in der URL, HTML bleibt frisch
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType text/html "access plus 0 seconds"
</IfModule>
<IfModule mod_headers.c>
  <FilesMatch "\.(webp|jpe?g|png|svg|woff2|css|js)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
  </FilesMatch>
  Header always set X-Content-Type-Options "nosniff"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# www erzwingen (kanonisch)
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{HTTP_HOST} ^fair-way-golf\.com$ [NC]
  RewriteRule ^(.*)$ https://www.fair-way-golf.com/$1 [R=301,L]
</IfModule>

# Kein Verzeichnislisting, keine Systemdateien
Options -Indexes
<FilesMatch "^(xmlrpc\.php|readme\.html|license\.txt|wp-config\.php|\.htaccess)$">
  Require all denied
</FilesMatch>

# Keine PHP-Ausführung in Uploads (Datei wp-content/uploads/.htaccess)
# <FilesMatch "\.php$">
#   Require all denied
# </FilesMatch>
```

Die letzte Regel gehört als eigene `.htaccess` nach `wp-content/uploads/`.
