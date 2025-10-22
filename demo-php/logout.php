<?php
require_once 'config.php';

if (isset($_SESSION['token'])) {
    // Get the logout URL from Keycloak
    $logoutUrl = $keycloak->getLogoutUrl([
        'redirect_uri' => 'http://localhost:4000/index.php',
        'access_token' => $_SESSION['token']
    ]);
    
    // Clear the session
    session_destroy();
    
    // Redirect to Keycloak logout (which will then redirect back to our app)
    header('Location: ' . $logoutUrl);
    exit;
}

// If no token, just clear session and redirect
session_destroy();
header('Location: /index.php');
exit;
