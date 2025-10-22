<?php
session_start();

require_once 'vendor/autoload.php';

$keycloak = new Stevenmaguire\Keycloak\Client([
    'authServerUrl' => 'http://localhost:8080',
    'realm'         => 'myrealm',
    'clientId'      => 'my-php-app',
    'clientSecret'  => 'your-client-secret', // Replace with your client secret if confidential
    'redirectUri'   => 'http://localhost:4000/index.php'
]);

?>
