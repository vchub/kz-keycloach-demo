<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['token'])) {
    $keycloak->logout([
        'redirect_uri' => 'http://localhost:4000/index.php',
        'id_token_hint' => $_SESSION['token']->getIdToken()
    ]);
}

session_destroy();
header('Location: /index.php');
exit;
