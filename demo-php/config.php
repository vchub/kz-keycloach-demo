<?php
require_once __DIR__ . '/vendor/autoload.php';

session_start();

$keycloak = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
    'authServerUrl' => 'http://localhost:8080',
    'realm'         => 'myrealm',
    'clientId'      => 'php-app',
    'clientSecret'  => 'BgMEn9XoAvIo9qwcE9BOoCBdVdhnufgi',
    'redirectUri'   => 'http://localhost:4000/index.php',
    'version'       => '20.0.0' // Use modern Keycloak URL structure
]);

?>
