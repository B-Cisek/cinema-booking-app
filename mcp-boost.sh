#!/bin/bash
cd "$(dirname "$0")"
docker compose exec -T app php artisan boost:mcp
