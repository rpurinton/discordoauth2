<?php

namespace RPurinton\DiscordOAuth2;

use RPurinton\{Config, HTTPS};

class DiscordOAuth2
{
    const SCOPE = "identify email";

    private Config $discord;

    public function __construct()
    {
        $this->discord = Config::open("DiscordOAuth2", [
            "web" => [
                "client_id"     => "string",
                "client_secret" => "string",
                "auth_uri"      => "string",
                "token_uri"     => "string",
            ],
        ]);
    }

    public function init()
    {
        $client_id = $this->discord->config['web']['client_id'];
        $client_secret = $this->discord->config['web']['client_secret'];
        $redirect_uri = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if (strpos($redirect_uri, "?") !== false) {
            $redirect_uri = substr($redirect_uri, 0, strpos($redirect_uri, "?"));
        }
        $first_url = $this->discord->config['web']['auth_uri'] . "?" . http_build_query([
            "client_id"              => $client_id,
            "redirect_uri"           => $redirect_uri,
            "scope"                  => self::SCOPE,
            "response_type"          => "code",
        ]);
        if (!isset($_GET["code"])) {
            header("Location: $first_url");
            exit;
        }
        $code = $_GET["code"];
        $response = HTTPS::request([
            'url' => $this->discord->config['web']['token_uri'],
            'method' => 'POST',
            'headers' => ['Content-Type: application/x-www-form-urlencoded'],
            'body' => http_build_query([
                'code'          => $code,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ]),
        ]);
        $response = json_decode($response, true);
        $this->discord->config['access_token'] = $response['access_token'];
        $this->discord->config['refresh_token'] = $response['refresh_token'];
        $this->discord->config['expires_at'] = time() + $response['expires_in'] - 30;
        $this->discord->save();
    }

    public function refresh_token()
    {
        $response = HTTPS::request([
            'url' => $this->discord->config['web']['token_uri'],
            'method' => 'POST',
            'headers' => ['Content-Type: application/x-www-form-urlencoded'],
            'body' => http_build_query([
                'client_id'     => $this->discord->config['web']['client_id'],
                'client_secret' => $this->discord->config['web']['client_secret'],
                'refresh_token' => $this->discord->config['refresh_token'],
                'grant_type'    => 'refresh_token',
            ]),
        ]);

        $response = json_decode($response, true);
        $this->discord->config['access_token'] = $response['access_token'];
        $this->discord->config['expires_at'] = time() + $response['expires_in'] - 30;
        $this->discord->save();
    }
}
