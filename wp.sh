#!/usr/bin/env bash
# WP-CLI wrapper for the fairwaygolf stack: ./wp.sh <wp-command>
# Übernimmt WORDPRESS_CONFIG_EXTRA und WORDPRESS_DEBUG aus dem laufenden Container, damit
# Konstanten wie FWG_SMTP_HOST (MailHog) und WP_DEBUG auch in der CLI gelten.
exec docker run --rm -i --user 33:33 \
  --volumes-from fwg_wordpress \
  --network fairwaygolf_default \
  -e WORDPRESS_DB_HOST=db \
  -e WORDPRESS_DB_USER=fwg \
  -e WORDPRESS_DB_PASSWORD=fwg_password \
  -e WORDPRESS_DB_NAME=fwg_wordpress \
  -e WORDPRESS_TABLE_PREFIX=fwg_ \
  -e "WORDPRESS_CONFIG_EXTRA=$(docker exec fwg_wordpress printenv WORDPRESS_CONFIG_EXTRA)" \
  -e "WORDPRESS_DEBUG=$(docker exec fwg_wordpress printenv WORDPRESS_DEBUG)" \
  wordpress:cli wp "$@"
