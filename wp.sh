#!/usr/bin/env bash
# WP-CLI wrapper for the fairwaygolf stack: ./wp.sh <wp-command>
exec docker run --rm -i --user 33:33 \
  --volumes-from fwg_wordpress \
  --network fairwaygolf_default \
  -e WORDPRESS_DB_HOST=db \
  -e WORDPRESS_DB_USER=fwg \
  -e WORDPRESS_DB_PASSWORD=fwg_password \
  -e WORDPRESS_DB_NAME=fwg_wordpress \
  wordpress:cli wp "$@"
