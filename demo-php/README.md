# Keycloak PHP OAuth2 Demo

A simple PHP application demonstrating OAuth 2.0 authentication and authorization using Keycloak as the identity provider.

## 🎯 Overview

This demo application showcases how to integrate Keycloak authentication into a PHP web application using the OAuth 2.0 Authorization Code Flow. Users can log in via Keycloak, view their profile information, and log out securely.

## ✨ Features

- 🔐 **OAuth 2.0 Authorization Code Flow** - Industry-standard secure authentication
- 👤 **User Profile Display** - Shows authenticated user information
- 🔄 **Automatic Token Refresh** - Seamlessly handles expired access tokens
- 🚪 **Secure Logout** - Logs out from both the app and Keycloak
- 📝 **User Registration** - Built-in support for new user sign-up
- 🛡️ **CSRF Protection** - State parameter validation
- 💾 **Session Management** - Secure server-side token storage

## 🚀 Quick Start

### Prerequisites

- PHP 7.4 or higher
- Composer
- Keycloak server running (default: http://localhost:8080)
- Configured Keycloak realm and client

### Installation

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Configure Keycloak connection:**
   Edit `config.php` with your Keycloak settings:
   ```php
   $keycloak = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
       'authServerUrl' => 'http://localhost:8080',
       'realm'         => 'myrealm',
       'clientId'      => 'php-app',
       'clientSecret'  => 'your-client-secret',
       'redirectUri'   => 'http://localhost:4000/index.php'
   ]);
   ```

3. **Start PHP development server:**
   ```bash
   php -S localhost:4000
   ```

4. **Visit the application:**
   ```
   http://localhost:4000/index.php
   ```

## 📁 Project Structure

```
demo-php/
├── config.php              # Keycloak provider configuration
├── index.php               # Main application with OAuth flow
├── logout.php              # Logout handler
├── composer.json           # PHP dependencies
├── guides/                 # Documentation
│   ├── app-architecture.md       # Detailed app architecture
│   ├── auth-flow.md              # OAuth flow explanation
│   └── REGISTRATION_GUIDE.md     # User registration setup
└── vendor/                 # Composer dependencies
```

## 📚 Documentation Guides

### 🏗️ [Application Architecture](guides/app-architecture.md)
- Complete code walkthrough
- How each component works
- Session management
- Security features
- Error handling

### 🔄 [Authentication Flow](guides/auth-flow.md)
- OAuth 2.0 Authorization Code Flow explained
- Step-by-step authentication process
- Token exchange and refresh
- User information retrieval
- Visual flow diagrams

### 📝 [User Registration Guide](guides/REGISTRATION_GUIDE.md)
- Enable user sign-up functionality
- Multiple registration options
- Keycloak configuration steps
- Custom registration forms
- Email verification setup

## 🔧 Configuration

### Keycloak Client Settings

Ensure your Keycloak client is configured with:

- **Access Type:** `confidential`
- **Valid Redirect URIs:** `http://localhost:4000/*`
- **Web Origins:** `http://localhost:4000`
- **Standard Flow Enabled:** `ON`

### Enable User Registration (Optional)

1. Go to Keycloak Admin Console
2. Select your realm
3. **Realm Settings** → **Login** tab
4. Toggle **User registration** to `ON`

See [User Registration Guide](guides/REGISTRATION_GUIDE.md) for detailed instructions.

## 🎮 Usage

### Login Flow

1. Visit the application homepage
2. Click "Login with Keycloak"
3. Enter your Keycloak credentials
4. You'll be redirected back with your profile information displayed

### Registration Flow

1. Visit the application homepage
2. Click "Create Account"
3. Fill out the registration form
4. You'll be automatically logged in and redirected back

### Logout

1. Click the "Logout" link
2. You'll be logged out from both the app and Keycloak
3. Redirected back to the homepage

## 🔒 Security Features

- **CSRF Protection** - State parameter validation prevents cross-site request forgery
- **Authorization Code Flow** - Most secure OAuth2 flow for server-side apps
- **Server-Side Sessions** - Tokens stored securely on the server
- **Automatic Token Refresh** - Handles expired tokens transparently
- **Secure Logout** - Invalidates tokens on both app and Keycloak

## 🛠️ Troubleshooting

### Common Issues

**"Invalid state parameter" error:**
- Clear browser cookies and try again
- Ensure sessions are working properly

**"Failed to get access token" error:**
- Verify Keycloak client credentials
- Check redirect URI matches exactly

**User can't login after registration:**
- Check if email verification is required
- Configure SMTP settings in Keycloak if needed

See the [Application Architecture Guide](guides/app-architecture.md#-troubleshooting) for more troubleshooting tips.

## 📦 Dependencies

- **stevenmaguire/oauth2-keycloak** - Keycloak provider for OAuth2 client
- **league/oauth2-client** - PHP OAuth 2.0 client library
- **firebase/php-jwt** - JSON Web Token implementation

## 🔗 Useful Links

- [Keycloak Documentation](https://www.keycloak.org/documentation)
- [OAuth 2.0 Specification](https://oauth.net/2/)
- [stevenmaguire/oauth2-keycloak](https://github.com/stevenmaguire/oauth2-keycloak)
- [PHP League OAuth2 Client](https://oauth2-client.thephpleague.com/)

## 📝 License

This is a demonstration project for educational purposes.

## 🤝 Contributing

Feel free to use this as a starting point for your own Keycloak integration projects!

---

**Need help?** Check out the detailed guides in the [`guides/`](guides/) directory.

*Last updated: October 22, 2025*
