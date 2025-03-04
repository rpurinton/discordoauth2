<?php

namespace RPurinton;

use RPurinton\{Config, HTTPS};

class DiscordOAuth2
{
    const SCOPE = "identify email";

    private array $config;

    public function __construct()
    {
        $this->config = Config::get("DiscordOAuth2", [
            "web" => [
                "client_id"     => "string",
                "client_secret" => "string",
                "auth_uri"      => "string",
                "token_uri"     => "string",
            ],
        ]);
    }

    public static function connect(): array
    {
        return (new self())->auth_token();
    }

    public static function refresh(string $refresh_token): array
    {
        return (new self())->refresh_token($refresh_token);
    }

    private function auth_token(): array
    {
        $client_id = $this->config['web']['client_id'];
        $client_secret = $this->config['web']['client_secret'];
        $redirect_uri = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if (strpos($redirect_uri, "?") !== false) {
            $redirect_uri = substr($redirect_uri, 0, strpos($redirect_uri, "?"));
        }
        $first_url = $this->config['web']['auth_uri'] . "?" . http_build_query([
            "client_id"              => $client_id,
            "redirect_uri"           => $redirect_uri,
            "response_type"          => "code",
            "scope"                  => self::SCOPE,
        ]);
        if (!isset($_GET["code"])) {
            header("Location: $first_url");
            exit;
        }
        $code = $_GET["code"];
        $response = HTTPS::request([
            'url' => $this->config['web']['token_uri'],
            'method' => 'POST',
            'headers' => ['Content-Type: application/x-www-form-urlencoded'],
            'body' => http_build_query([
                'code'          => $code,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $redirect_uri,
                'scope'         => self::SCOPE,
            ]),
        ]);
        return json_decode($response, true);
    }

    private function refresh_token(string $refresh_token): array
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
