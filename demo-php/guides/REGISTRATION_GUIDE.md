# User Registration Guide for Keycloak PHP Demo

This guide explains how to enable and implement user registration (sign-up) functionality in your Keycloak-integrated PHP application.

## 🎯 Available Options

### Option 1: Keycloak Built-in Registration (Recommended - Easiest)
### Option 2: Direct Registration Link from Your App (Implemented)
### Option 3: Custom Registration Form with Keycloak Admin API
### Option 4: Self-Service Registration Portal

---

## ✅ Option 1: Enable Keycloak's Built-in Registration

### Steps to Enable:

1. **Login to Keycloak Admin Console**
   ```
   URL: http://localhost:8080
   Username: admin
   Password: admin
   ```

2. **Configure Realm Settings**
   - Select your realm: `myrealm`
   - Click **Realm Settings** in the left menu
   - Go to the **Login** tab
   - Toggle **User registration** to `ON`
   - Click **Save**

3. **Optional Configuration:**
   
   Additional settings you can enable:
   - ✅ **Email as username** - Users login with email instead of username
   - ✅ **Require email verification** - Users must verify email before login
   - ✅ **Forgot password** - Users can reset password via email
   - ✅ **Remember me** - Allow users to stay logged in
   - ✅ **Verify email** - Require email verification during registration

### Result:
When users visit the Keycloak login page, they'll see:
- "Register" link at the bottom
- Clicking it shows a registration form with:
  - First Name
  - Last Name
  - Email
  - Username
  - Password
  - Password Confirmation

---

## 🔗 Option 2: Direct Registration Link (Already Implemented!)

### What Was Added:

#### 1. **Registration Button in UI** (index.php)
```php
<a href="?register=1">Create Account</a>
```

#### 2. **Registration Handler** (index.php)
```php
if (isset($_GET['register'])) {
    $authUrl = $keycloak->getAuthorizationUrl([
        'kc_action' => 'REGISTER'
    ]);
    $_SESSION['oauth2state'] = $keycloak->getState();
    header('Location: ' . $authUrl);
    exit;
}
```

### How It Works:

1. User clicks "Create Account" button
2. App redirects to Keycloak with `kc_action=REGISTER` parameter
3. Keycloak shows registration form instead of login form
4. After registration, Keycloak redirects back to your app
5. User is automatically logged in

### Prerequisites:
- You must first enable **User registration** in Keycloak (see Option 1)
- Otherwise, the registration page won't be accessible

---

## 🛠 Option 3: Custom Registration Form with Admin API

If you want a registration form directly in your app (not redirecting to Keycloak), you can use the Keycloak Admin REST API.

### Implementation Steps:

#### 1. Create a Service Account in Keycloak

1. Go to **Clients** → **php-app**
2. Set **Service Accounts Enabled** to `ON`
3. Save
4. Go to **Service Account Roles** tab
5. Assign roles:
   - Client Roles → realm-management → `manage-users`

#### 2. Create register.php

```php
<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];
    
    // Get admin access token
    $tokenUrl = 'http://localhost:8080/realms/myrealm/protocol/openid-connect/token';
    $tokenData = [
        'grant_type' => 'client_credentials',
        'client_id' => 'php-app',
        'client_secret' => 'BgMEn9XoAvIo9qwcE9BOoCBdVdhnufgi'
    ];
    
    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $tokenInfo = json_decode($response, true);
    $adminToken = $tokenInfo['access_token'];
    curl_close($ch);
    
    // Create user
    $createUserUrl = 'http://localhost:8080/admin/realms/myrealm/users';
    $userData = [
        'username' => $username,
        'email' => $email,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'enabled' => true,
        'emailVerified' => false,
        'credentials' => [
            [
                'type' => 'password',
                'value' => $password,
                'temporary' => false
            ]
        ]
    ];
    
    $ch = curl_init($createUserUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $adminToken
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 201) {
        echo "User registered successfully!";
        // Optionally, automatically log them in
        header('Location: ?login=1');
        exit;
    } else {
        echo "Registration failed: " . $response;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Create Account</h1>
    <form method="post">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>First Name: <input type="text" name="firstName" required></label><br>
        <label>Last Name: <input type="text" name="lastName" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
```

#### 3. Add link to register.php
```php
<a href="register.php">Create Account (Custom Form)</a>
```

### Pros and Cons:

**Pros:**
- Full control over registration form UI/UX
- Can add custom fields
- Can integrate with your existing design
- Can add additional validation

**Cons:**
- More code to maintain
- Need to manage Admin API credentials securely
- Need to handle errors manually
- More security considerations

---

## 🎨 Option 4: Self-Service Registration Portal

Keycloak has an Account Console where users can manage their profile.

### Enable Account Console:

1. Users visit: `http://localhost:8080/realms/myrealm/account`
2. Click "Sign in" → Then "Register"
3. Complete registration form
4. Manage profile, password, sessions, applications

This is useful for users to:
- Update profile information
- Change password
- Manage sessions
- View consent history
- Enable two-factor authentication

---

## 🔐 Security Considerations

### Email Verification

**Highly Recommended!** Enable email verification:

1. In Keycloak Admin Console
2. Go to **Realm Settings** → **Login**
3. Enable **Verify email**
4. Configure SMTP settings in **Realm Settings** → **Email**

**SMTP Configuration Example:**
```
Host: smtp.gmail.com
Port: 587
From: noreply@yourapp.com
Enable StartTLS: ON
Authentication: ON
Username: your-email@gmail.com
Password: your-app-password
```

### Password Policy

Set strong password requirements:

1. Go to **Authentication** → **Password Policy**
2. Add policies:
   - Minimum length: 8 characters
   - At least 1 uppercase letter
   - At least 1 lowercase letter
   - At least 1 digit
   - At least 1 special character
   - Not recently used
   - Not username

### User Validation

Enable captcha to prevent bot registrations:

1. Go to **Realm Settings** → **Security Defenses**
2. Configure **reCAPTCHA**
3. Add your reCAPTCHA site key and secret

---

## 📊 Comparison of Options

| Feature | Built-in | Direct Link | Custom API | Account Console |
|---------|----------|-------------|------------|-----------------|
| **Ease of Setup** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ |
| **Customization** | ⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Maintenance** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ |
| **Security** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **User Experience** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |

**Recommendation:** Use **Built-in + Direct Link** (Options 1 & 2) for most applications. It's secure, easy to maintain, and provides a good user experience.

---

## 🧪 Testing Registration

### Test the Registration Flow:

1. **Clear your session/cookies**
2. **Visit your app:** http://localhost:4000/index.php
3. **Click "Create Account"**
4. **Fill out the registration form:**
   - First Name: Test
   - Last Name: User
   - Email: test@example.com
   - Username: testuser
   - Password: Test123!
5. **Submit the form**
6. **Verify you're redirected back and logged in**

### Check User in Keycloak:

1. Go to Keycloak Admin Console
2. Select **Users** from the menu
3. Click **View all users**
4. You should see your newly registered user

---

## 🎨 Customizing Registration Form

### Customize Keycloak Theme

You can customize the look of the registration page:

1. **Create custom theme:**
   ```bash
   mkdir -p keycloak/themes/mytheme/login
   ```

2. **Add custom CSS:**
   ```css
   /* themes/mytheme/login/resources/css/login.css */
   .login-pf body {
       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
   }
   ```

3. **Configure theme in Keycloak:**
   - Go to **Realm Settings** → **Themes**
   - Select your custom theme for **Login theme**

### Add Custom Fields

To add custom fields to registration:

1. Go to **Realm Settings** → **User Profile**
2. Click **Create attribute**
3. Add custom attributes like:
   - Phone number
   - Company name
   - Country
   - etc.

---

## 📝 Current Implementation Summary

### What's Now Available in Your App:

✅ **Two buttons on login page:**
- "Login with Keycloak" - For existing users
- "Create Account" - For new users

✅ **Registration flow:**
- Click "Create Account"
- Redirected to Keycloak registration form
- Fill out registration details
- Automatically logged in after registration
- Redirected back to your app

✅ **Security:**
- State parameter for CSRF protection
- OAuth2 Authorization Code Flow
- Secure token handling

### Next Steps:

1. ✅ Enable "User registration" in Keycloak Realm Settings
2. ✅ Test the registration flow
3. 🔧 Configure email verification (optional but recommended)
4. 🔧 Set password policy (optional but recommended)
5. 🎨 Customize registration form theme (optional)

---

## 🆘 Troubleshooting

### "Register" link not showing on Keycloak page
**Solution:** Enable "User registration" in Realm Settings → Login

### Registration form shows 404
**Solution:** Check that the realm name is correct in URLs

### Users can register but can't login
**Solution:** Check that "Email verification" is not required, or configure SMTP

### Custom fields not showing
**Solution:** Configure User Profile attributes in Realm Settings

---

*Last updated: October 22, 2025*
