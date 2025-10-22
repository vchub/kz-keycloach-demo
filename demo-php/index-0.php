<?php

require 'vendor/autoload.php';

session_start();

$provider = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
    'authServerUrl' => 'http://localhost:8080',
    'realm'         => 'myrealm',
    'clientId'      => 'php-app',
    'clientSecret'  => 'BgMEn9XoAvIo9qwcE9BOoCBdVdhnufgi',
    'redirectUri'   => 'http://localhost:4000/index.php',
    'version'       => '20.0.0' // Use modern Keycloak URL structure
]);

/* $provider = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([ */
/*     'authServerUrl'             => '', */
/*     'realm'                     => '', */
/*     'clientId'                  => '', */
/*     'clientSecret'              => '', */
/*     'redirectUri'               => '', */
/*     'encryptionAlgorithm'       => null, */
/*     'encryptionKey'             => null, */
/*     'encryptionKeyPath'         => null */
/* ]); */

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state, make sure HTTP sessions are enabled.');
} else {
    // Try to get an access token (using the authorization coe grant)
    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch (Exception $e) {
        exit('Failed to get access token: '.$e->getMessage());
    }

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);
        // Use these details to create a new profile
        printf('Hello %s!\n<br>', $user->getName());
        echo '<pre>';
        print_r($user->toArray());
        echo '</pre>';

    } catch (Exception $e) {
        echo 'Failed to get resource owner: '.$e->getMessage() . '<br>';
        echo 'Debug info: <pre>';
        echo 'Token: ' . substr($token->getToken(), 0, 50) . '...' . "\n";
        echo 'Resource Owner URL: ' . $provider->getResourceOwnerDetailsUrl($token) . "\n\n";
        
        // Try to manually fetch the userinfo to see what we get
        echo "Attempting manual fetch of userinfo:\n";
        $ch = curl_init($provider->getResourceOwnerDetailsUrl($token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token->getToken(),
            'Accept: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        echo "Content-Type: $contentType\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
        echo '</pre>';
        exit;
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
