### This PHP application implements OAuth 2.0 authentication with Keycloak

Flow:

1. User visits index.php
   ↓
2. Not logged in → Shows "Login with Keycloak" link
   ↓
3. User clicks link → Goes to ?login=1
   ↓
4. PHP generates auth URL and redirects to Keycloak
   ↓
5. User logs in at Keycloak
   ↓
6. Keycloak redirects back with ?code=XXX&state=YYY
   ↓
7. PHP verifies state, exchanges code for token
   ↓
8. Token stored in session, redirect to clean URL
   ↓
9. PHP fetches user info using token
   ↓
10. Shows authenticated page with user details

---

example of the login url:
<http://localhost:8080/realms/myrealm/protocol/openid-connect/auth?state=1b324dea1e7fb4da154472148f8651fd&scope=profile%20email%20openid&response_type=code&approval_prompt=auto&redirect_uri=http%3A%2F%2Flocalhost%3A4000%2Findex.php&client_id=php-app>
