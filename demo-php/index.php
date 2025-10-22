<?php
require_once 'config.php';

if (isset($_GET['code'])) {
    try {
        $token = $keycloak->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        $_SESSION['token'] = $token;
        header('Location: /index.php');
        exit;
    } catch (Exception $e) {
        die('Failed to get access token: ' . $e->getMessage());
    }
}

if (isset($_SESSION['token'])) {
    try {
        $resourceOwner = $keycloak->getResourceOwner($_SESSION['token']);
    } catch (Exception $e) {
        // Token might be expired, try to refresh it
        try {
            $newToken = $keycloak->getAccessToken('refresh_token', [
                'refresh_token' => $_SESSION['token']->getRefreshToken()
            ]);
            $_SESSION['token'] = $newToken;
            $resourceOwner = $keycloak->getResourceOwner($newToken);
        } catch (Exception $e) {
            // If refresh fails, clear session and re-authenticate
            unset($_SESSION['token']);
            header('Location: /index.php');
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keycloak PHP Demo</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        pre { background-color: #f4f4f4; padding: 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Keycloak PHP Demo</h1>
        <?php if (isset($resourceOwner)): ?>
            <h2>Welcome, <?php echo htmlspecialchars($resourceOwner->getName()); ?>!</h2>
            <p>You are authenticated.</p>
            <h3>User Details:</h3>
            <pre><?php print_r($resourceOwner->toArray()); ?></pre>
            <h3>Access Token:</h3>
            <pre><?php echo htmlspecialchars($_SESSION['token']->getToken()); ?></pre>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <p>You are not authenticated.</p>
            <a href="<?php echo $keycloak->getAuthorizationUrl(); ?>">Login with Keycloak</a>
        <?php endif; ?>
    </div>
</body>
</html>
