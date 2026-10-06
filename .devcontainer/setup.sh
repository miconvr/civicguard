#!/usr/bin/env bash
set -e

[ -f .env ] || cp .env.example .env

# Use the same PHP version Sail is configured for
v=$(grep -oE 'runtimes/8\.[0-9]+' docker-compose.yml | head -1 | grep -oE '8\.[0-9]+' | tr -d '.')
v=${v:-83}

if [ ! -d vendor ]; then
  docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    "laravelsail/php${v}-composer:latest" composer install --ignore-platform-reqs --no-interaction
fi

PORT=8000
URL="https://${CODESPACE_NAME}-${PORT}.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"

set_env() {
  if grep -q "^$1=" .env; then sed -i "s|^$1=.*|$1=$2|" .env; else echo "$1=$2" >> .env; fi
}
set_env APP_PORT "$PORT"
set_env APP_URL "$URL"
set_env APP_DEBUG false
[ -n "${GEMINI_API_KEY:-}" ] && set_env GEMINI_API_KEY "$GEMINI_API_KEY"

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate --force

for i in $(seq 1 30); do
  ./vendor/bin/sail artisan migrate --seed --force && break
  echo "Waiting for the database ($i/30)..."
  sleep 5
done

./vendor/bin/sail artisan storage:link || true
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
echo "Setup finished. Open the forwarded port 8000."
