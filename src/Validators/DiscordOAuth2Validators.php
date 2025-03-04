<?php

namespace RPurinton\Validators;

use RPurinton\Exceptions\DiscordOAuth2ValidationException;

class DiscordOAuth2Validators
{
    public static function validateClientID(string|int $client_id): bool
    {
        if (empty($client_id)) throw new DiscordOAuth2ValidationException("Client ID cannot be empty.");
        return true;
    }

    public static function validateClientSecret(string $client_secret): bool
    {
        if (empty($client_secret)) throw new DiscordOAuth2ValidationException("Client Secret cannot be empty.");
        return true;
    }

    public static function validateAuthURI(string $auth_uri): bool
    {
        if (empty($auth_uri)) throw new DiscordOAuth2ValidationException("Auth URI cannot be empty.");
        return true;
    }

    public static function validateTokenURI(string $token_uri): bool
    {
        if (empty($token_uri)) throw new DiscordOAuth2ValidationException("Token URI cannot be empty.");
        return true;
    }

    public static function validateInfoURI(string $info_uri): bool
    {
        if (empty($info_uri)) throw new DiscordOAuth2ValidationException("Info URI cannot be empty.");
        return true;
    }
}
