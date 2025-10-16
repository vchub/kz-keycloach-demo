# Keycloak Docker Setup Guide

## Step-by-Step Instructions to Run Keycloak with Your Demo Page

### Prerequisites

- Docker installed on your system
- A web browser
- The `index.html` file in your project

---

## Step 1: Run Keycloak in Docker

Start Keycloak using Docker with the following command:

```bash
docker run -d \
  --name keycloak-demo \
  -p 8080:8080 \
  -e KEYCLOAK_ADMIN=admin \
  -e KEYCLOAK_ADMIN_PASSWORD=admin \
  quay.io/keycloak/keycloak:24.0.3 \
  start-dev
```

**What this does:**

- `-d`: Runs container in detached mode (background)
- `--name keycloak-demo`: Names the container for easy reference
- `-p 8080:8080`: Maps port 8080 from container to your localhost
- `-e KEYCLOAK_ADMIN=admin`: Sets admin username
- `-e KEYCLOAK_ADMIN_PASSWORD=admin`: Sets admin password
- `start-dev`: Runs Keycloak in development mode (not for production!)

**Wait for Keycloak to start** (about 30-60 seconds). Check logs:

```bash
docker logs -f keycloak-demo
```

Look for a message like: `Keycloak 24.0.3 on JVM ... started in ...ms`

Press `Ctrl+C` to exit logs.

---

## Step 2: Access Keycloak Admin Console

1. Open your browser and navigate to: **<http://localhost:8080>**
2. Click on **"Administration Console"**
3. Login with:
   - **Username:** `admin`
   - **Password:** `admin`

---

## Step 3: Create a Realm

1. In the Keycloak admin console, hover over the dropdown at the top left (says "Keycloak" or "Master")
2. Click **"Create Realm"**
3. Enter the realm name: **`myrealm`**
4. Click **"Create"**

---

## Step 4: Create a Client

1. In the left sidebar, click **"Clients"**
2. Click **"Create client"** button
3. **General Settings:**
   - **Client type:** `OpenID Connect`
   - **Client ID:** `myclient`
   - Click **"Next"**

4. **Capability config:**
   - **Client authentication:** `OFF` (this is a public client)
   - **Authorization:** `OFF`
   - **Authentication flow:** Check these boxes:
     - ✅ Standard flow
     - ✅ Direct access grants
   - Click **"Next"**

5. **Login settings:**
   - **Root URL:** `http://localhost:3000`
   - **Home URL:** `http://localhost:3000`
   - **Valid redirect URIs:** `http://localhost:3000/*`
   - **Valid post logout redirect URIs:** `http://localhost:3000/*`
   - **Web origins:** `http://localhost:3000`
   - Click **"Save"**

---

## Step 5: Create a Test User

1. In the left sidebar, click **"Users"**
2. Click **"Add user"** button
3. Fill in the form:
   - **Username:** `testuser`
   - **Email:** `testuser@example.com`
   - **First name:** `Test`
   - **Last name:** `User`
   - **Email verified:** `ON` (toggle it)
4. Click **"Create"**

5. **Set a password:**
   - Click on the **"Credentials"** tab
   - Click **"Set password"**
   - **Password:** `password` (or choose your own)
   - **Password confirmation:** `password`
   - **Temporary:** `OFF` (toggle it off)
   - Click **"Save"**
   - Confirm by clicking **"Save password"**

---

## Step 6: Update Keycloak Configuration (Already Done!)

Your `index.html` already has the correct configuration:

```javascript
const keycloakConfig = {
  url: "http://localhost:8080",
  realm: "myrealm",
  clientId: "myclient",
};
```

**Note:** Keycloak 17+ uses a new URL structure. If you see authentication errors, you might need to change the URL.

---

## Step 7: Serve Your HTML File

You need to serve the HTML file via HTTP (not just open it as a file). Use one of these methods:

### Option A: Python Simple Server

```bash
# Python 3
python3 -m http.server 3000

# Python 2
python -m SimpleHTTPServer 3000
```

### Option B: Node.js http-server

```bash
# Install (if not already installed)
npm install -g http-server

# Run
http-server -p 3000
```

### Option C: PHP Built-in Server

```bash
php -S localhost:3000
```

---

## Step 8: Test the Authentication Flow

1. Open your browser to: **<http://localhost:3000>**
2. You should see the Keycloak Demo page showing "Not Authenticated"
3. Click the **"Log In"** button
4. You'll be redirected to Keycloak login page
5. Enter credentials:
   - **Username:** `testuser`
   - **Password:** `password`
6. Click **"Sign In"**
7. You should be redirected back to your demo page
8. You should now see:
   - Status: "Authenticated!"
   - User information displayed
   - Access token snippet
   - "Log Out" button

---

## Troubleshooting

### Issue: "Failed to fetch" or CORS errors

**Solution 1:** Check Keycloak URL - Modern Keycloak (17+) doesn't use `/auth` path:

Update your `index.html` configuration:

```javascript
const keycloakConfig = {
  url: "http://localhost:8080", // Remove /auth
  realm: "myrealm",
  clientId: "myclient",
};
```

**Solution 2:** Ensure Web origins is set correctly in the client configuration:

- Go to Clients → myclient → Settings
- Set **Web origins** to `http://localhost:3000`
- Click Save

### Issue: Redirect loop or "Invalid redirect URI"

**Solution:** Verify redirect URIs in client settings:

- Valid redirect URIs: `http://localhost:3000/*`
- Valid post logout redirect URIs: `http://localhost:3000/*`

### Issue: Can't access Keycloak admin console

**Solution:** Wait a bit longer for Keycloak to start, or check logs:

```bash
docker logs keycloak-demo
```

### Issue: Container stops immediately

**Solution:** Check Docker logs for errors:

```bash
docker logs keycloak-demo
```

---

## Useful Commands

### Stop Keycloak

```bash
docker stop keycloak-demo
```

### Start Keycloak again

```bash
docker start keycloak-demo
```

### Remove container (will lose all data)

```bash
docker rm -f keycloak-demo
```

### Use PostgreSQL for persistent data

```bash
docker run -d \
  --name keycloak-demo \
  -p 8080:8080 \
  -e KEYCLOAK_ADMIN=admin \
  -e KEYCLOAK_ADMIN_PASSWORD=admin \
  -e KC_DB=postgres \
  -e KC_DB_URL=jdbc:postgresql://your-db-host/keycloak \
  -e KC_DB_USERNAME=keycloak \
  -e KC_DB_PASSWORD=password \
  quay.io/keycloak/keycloak:24.0.3 \
  start-dev
```

---

## Production Considerations

⚠️ **This setup is for DEVELOPMENT ONLY**

For production:

1. Use `start` instead of `start-dev`
2. Enable HTTPS/TLS
3. Use a proper database (PostgreSQL, MySQL)
4. Set strong admin credentials
5. Configure proper hostname
6. Review security settings
7. Set up proper CORS policies

---

## Next Steps

Once authentication is working:

- Explore token contents using jwt.io
- Add role-based access control (RBAC)
- Implement protected API calls
- Add more users and groups
- Configure social login providers (Google, GitHub, etc.)

---

## Clear cookies and etc.

   ```js
   localStorage.clear();
   sessionStorage.clear();
   location.reload();
   ```
---

## Quick Reference

- **Keycloak Admin:** <http://localhost:8080>
- **Demo App:** <http://localhost:3000>
- **Admin User:** admin / admin
- **Test User:** testuser / password
- **Realm:** myrealm
- **Client ID:** myclient
