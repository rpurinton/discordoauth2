<?php

namespace RPurinton;

use RPurinton\{Config, HTTPS};
use RPurinton\Validators\DiscordOAuth2Validators;
use RPurinton\Exceptions\DiscordOAuth2Exception;

class DiscordOAuth2
{
    const SCOPE = "identify";

    private array $config;

    public function __construct()
    {
        try {
            $this->config = Config::get("DiscordOAuth2", [
                "web" => [
                    "client_id"     => DiscordOAuth2Validators::validateClientID(...),
                    "client_secret" => DiscordOAuth2Validators::validateClientSecret(...),
                    "auth_uri"      => DiscordOAuth2Validators::validateAuthURI(...),
                    "token_uri"     => DiscordOAuth2Validators::validateTokenURI(...),
                    "info_uri"      => DiscordOAuth2Validators::validateInfoURI(...),
                ],
            ]);
        } catch (\Exception $e) {
            throw new DiscordOAuth2Exception($e->getMessage());
        }
    }

    public static function init(): array
    {
        try {
            return (new self)->initialize();
        } catch (\Exception $e) {
            throw new DiscordOAuth2Exception($e->getMessage());
        }
    }

    protected function initialize(): array
    {
        try {
            $client_id = $this->config['web']['client_id'];
            $client_secret = $this->config['web']['client_secret'];
            $redirect_uri = explode('?', 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])[0];
            if (!isset($_GET["code"])) {
                header('Location: ' . $this->config['web']['auth_uri'] . "?" . http_build_query([
                    "client_id"              => $client_id,
                    "redirect_uri"           => $redirect_uri,
                    "response_type"          => "code",
                    "scope"                  => self::SCOPE,
                ]));
                exit;
            }
            return json_decode(HTTPS::request([
                'url' => $this->config['web']['token_uri'],
                'method' => 'POST',
                'headers' => ['Content-Type: application/x-www-form-urlencoded'],
                'body' => http_build_query([
                    'code'          => $_GET["code"],
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => $redirect_uri,
                    'scope'         => self::SCOPE,
                ]),
            ]), true);
        } catch (\Exception $e) {
            throw new DiscordOAuth2Exception($e->getMessage());
        }
    }

    public static function info(string $access_token): array
    {
        try {
            return (new self)->information($access_token);
        } catch (\Exception $e) {
            throw new DiscordOAuth2Exception($e->getMessage());
        }
    }

    protected function information(string $access_token): array
    {
        if (empty($access_token)) throw new \Exception("Access token is empty.");
        try {
            $response = HTTPS::request([
                'url' => $this->config['web']['info_uri'],
                'headers' => ['Authorization: Bearer ' . $access_token],
            ]);
            return json_decode($response, true);
        } catch (\Exception $e) {
            throw new DiscordOAuth2Exception($e->getMessage());
        }
    }

    public static function refresh(string $refresh_token): array
    {
        try {
            return (new self)->refresh_token($refresh_token);
        } catch (\Exception $e) {
            throw new DiscordOAuth2Exception($e->getMessage());
        }
    }

    protected function refresh_token(string $refresh_token): array
    {
        $response = HTTPS::request([
            'url' => $this->config['web']['token_uri'],
            'method' => 'POST',
            'headers' => ['Content-Type: application/x-www-form-urlencoded'],
            'body' => http_build_query([
                'client_id'     => $this->config['web']['client_id'],
                'client_secret' => $this->config['web']['client_secret'],
                'refresh_token' => $refresh_token,
                'grant_type'    => 'refresh_token',
            ]),
        ]);
        return json_decode($response, true);
    }
}
