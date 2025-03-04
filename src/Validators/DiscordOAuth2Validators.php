<?php

namespace RPurinton\Validators;

use RPurinton\Exceptions\DiscordOAuth2ValidationException;

class DiscordOAuth2Validators
{
    public static function validateClientID(string $client_id): void
    {
        if (empty($client_id)) throw new DiscordOAuth2ValidationException("Client ID cannot be empty.");
    }

    public static function validateClientSecret(string $client_secret): void
    {
        if (empty($client_secret)) throw new DiscordOAuth2ValidationException("Client Secret cannot be empty.");
    }

    public static function validateAuthURI(string $auth_uri): void
    {
        if (empty($auth_uri)) throw new DiscordOAuth2ValidationException("Auth URI cannot be empty.");
    }

    public static function validateTokenURI(string $token_uri): void
    {
        if (empty($token_uri)) throw new DiscordOAuth2ValidationException("Token URI cannot be empty.");
    }

    public static function validateInfoURI(string $info_uri): void
    {
        if (empty($info_uri)) throw new DiscordOAuth2ValidationException("Info URI cannot be empty.");
    }
}
