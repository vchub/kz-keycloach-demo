#!/bin/bash

echo "🛑 Stopping Keycloak Demo Environment..."
echo ""

# Stop and remove container
docker stop keycloak-demo 2>/dev/null
docker rm keycloak-demo 2>/dev/null

echo "✅ Keycloak stopped and removed."
echo ""
echo "To start again, run: ./start.sh"
echo ""
