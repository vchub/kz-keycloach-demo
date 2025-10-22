#!/bin/bash

echo "🚀 Starting Keycloak Demo Environment..."
echo ""

# Start Keycloak
echo "📦 Starting Keycloak container..."
docker run -d \
  --name keycloak-demo \
  -p 8080:8080 \
  -e KEYCLOAK_ADMIN=admin \
  -e KEYCLOAK_ADMIN_PASSWORD=admin \
  quay.io/keycloak/keycloak:26.4.0 \
  start-dev

echo ""
echo "⏳ Waiting for Keycloak to start (this may take 30-60 seconds)..."
echo ""

# Wait for Keycloak to be ready
max_attempts=60
attempt=0
until curl -s http://localhost:8080/health/ready > /dev/null 2>&1 || [ $attempt -eq $max_attempts ]; do
    printf "."
    sleep 2
    attempt=$((attempt+1))
done

echo ""
echo ""

if [ $attempt -eq $max_attempts ]; then
    echo "❌ Keycloak failed to start. Check logs with: docker logs keycloak-demo"
    exit 1
fi

echo "✅ Keycloak is running!"
echo ""
echo "📋 Quick Setup Steps:"
echo "   1. Open Keycloak Admin: http://localhost:8080"
echo "   2. Login with: admin / admin"
echo "   3. Create realm: 'my-realm'"
echo "   4. Create client: 'my-spa-client' (public, redirect URI: http://localhost:3000/*)"
echo "   5. Create user: 'testuser' with password 'password'"
echo ""
echo "🌐 Start the demo app:"
echo "   python3 -m http.server 3000"
echo ""
echo "🛑 To stop Keycloak:"
echo "   docker stop keycloak-demo"
echo ""
