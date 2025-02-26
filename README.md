# RPurinton Discord OAuth2 Library for PHP

## Introduction

The RPurinton Discord OAuth2 Library is a PHP library designed to facilitate OAuth2 authentication with Discord. It provides functionality for obtaining and refreshing access tokens using Discord's API.

## Installation

### Composer Installation

To install the library, use Composer:

```bash
composer require rpurinton/discordoauth2
```

### Configuration Setup

1. Create a `config` directory at the same level as the `vendor` directory.
2. Place the `DiscordOAuth2.json` file with your credentials in the `config` directory.
   - Note: The `access_token`, `refresh_token`, and `expires_at` will be added later by the library.

## Discord Developer Portal Setup

1. Create a Discord application in the [Discord Developer Portal](https://discord.com/developers/applications).
2. Enable OAuth2 and set up the necessary redirect URIs.
3. Obtain the client ID and client secret.
4. Download the credentials JSON file and place it in the `config` directory as `DiscordOAuth2.json`.

## Usage

### Initialization

- Create a new instance of the `DiscordOAuth2` class.
- Run the `->init()` method to start the OAuth2 authentication flow.
- This method will redirect to Discord for authentication and back to your script with the authorization code.
- The `init()` method is crucial for the first-time setup to obtain the `access_token` and `refresh_token`.
- These tokens are stored in the `DiscordOAuth2.json` configuration file.
- Once the tokens are obtained, the `init()` method is not required again unless the user deauthorizes the app from their Discord account.

### API Methods

- **`init()`**: Initiates the OAuth2 flow to obtain access and refresh tokens.
- **`refresh_token()`**: Refreshes the access token using the refresh token.

## Examples

```php
<?php
require 'vendor/autoload.php';

use RPurinton\DiscordOAuth2\DiscordOAuth2;

$discord = new DiscordOAuth2();
$discord->init(); // Only needed for the first-time setup
```

## Troubleshooting

- Ensure that the `DiscordOAuth2.json` file is correctly configured and accessible.
- Verify that the Discord application is set up with the correct credentials and permissions.
- If you encounter issues with token expiration, ensure the `refresh_token` is valid and the `init()` method was successfully executed initially.

## License

This library is licensed under the MIT License. See the LICENSE file for more information.
