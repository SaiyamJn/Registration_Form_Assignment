#!/bin/sh
# Try running migrations (retry a few times in case DB not yet ready)
TRIES=0
MAX=6
until php migrate.php || [ $TRIES -ge $MAX ]; do
  TRIES=$((TRIES+1))
  echo "Migration attempt $TRIES failed - sleeping 3s"
  sleep 3
done

# Start Apache in foreground (official image entrypoint)
exec apache2-foreground
