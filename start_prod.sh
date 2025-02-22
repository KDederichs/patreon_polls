docker compose -f compose.yaml -f compose.prod.yaml build php pwa
docker compose -f compose.yaml -f compose.prod.yaml up -d
