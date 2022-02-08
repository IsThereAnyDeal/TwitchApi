<?php
namespace IsThereAnyDeal\Twitch\Api;

use GuzzleHttp\Client as GuzzleClient;

class Authorization {
    private const AuthEndpoint = "https://id.twitch.tv/oauth2/token";

    private Credentials $credentials;
    private TokenStorageInterface $tokenStorage;
    private GuzzleClient $guzzle;

    public function __construct(Credentials $credentials, TokenStorageInterface $tokenStorage, GuzzleClient $guzzle) {
        $this->credentials = $credentials;
        $this->tokenStorage = $tokenStorage;
        $this->guzzle = $guzzle;
    }

    public function getToken(): ?Token {

        $token = $this->tokenStorage->get();
        if (!is_null($token)) {
            return $token;
        }

        $response = $this->guzzle->post(self::AuthEndpoint, [
            "query" => [
                "client_id" => $this->credentials->getClientId(),
                "client_secret" => $this->credentials->getClientSecret(),
                "grant_type" => "client_credentials"
            ]
        ]);

        $body = (string)$response->getBody();
        $json = json_decode($body, true);

        if ($json !== false && isset($json['access_token'])) {
            $token = $json['access_token'];
            $expiry = $json['expires_in'];

            $token = new Token($token);
            $this->tokenStorage->set($token, $expiry);
            return $token;
        }

        return null;
    }
}
