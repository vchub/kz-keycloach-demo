# Auth w/ Keycloack

Source: <https://www.keycloak.org/getting-started/getting-started-docker>

---

Start Keycloak
From a terminal, enter the following command to start Keycloak:

    docker run -p 127.0.0.1:8080:8080 -e KC_BOOTSTRAP_ADMIN_USERNAME=admin -e KC_BOOTSTRAP_ADMIN_PASSWORD=admin quay.io/keycloak/keycloak:26.4.0 start-dev

This command starts Keycloak exposed on the local port 8080 and creates an initial admin user with the username admin and password admin.

---
