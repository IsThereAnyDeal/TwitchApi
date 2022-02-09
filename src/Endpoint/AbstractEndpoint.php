<?php
namespace IsThereAnyDeal\Twitch\Api\Endpoint;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use IsThereAnyDeal\Twitch\Api\Credentials;
use IsThereAnyDeal\Twitch\Api\Exception\InvalidResponseException;
use IsThereAnyDeal\Twitch\Api\Exception\RateLimitedException;
use IsThereAnyDeal\Twitch\Api\Exception\UnsupportedResponseTypeException;
use IsThereAnyDeal\Twitch\Api\Response\AbstractResponse;
use IsThereAnyDeal\Twitch\Api\Response\DataResponse;
use IsThereAnyDeal\Twitch\Api\Response\ErrorResponse;
use IsThereAnyDeal\Twitch\Api\Token;

abstract class AbstractEndpoint {

    private const ApiHost = "https://api.twitch.tv/helix/";

    private Credentials $credentials;
    private GuzzleClient $client;

    private ?Token $token = null;

    private ?int $rateLimit = null;
    private ?int $rateLimitRemaining = null;

    public function __construct(Credentials $credentials, GuzzleClient $client) {
        $this->credentials = $credentials;
        $this->client = $client;
    }

    public function setToken(?Token $token): self {
        if (!is_null($token)) {
            $this->token = $token;
        }
        return $this;
    }

    private function getHeaders(): array {
        $headers = [
            "Client-Id" => $this->credentials->getClientId()
        ];

        if (!is_null($this->token)) {
            $headers['Authorization'] = "Bearer {$this->token}";
        }

        return $headers;
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param string $method
     * @return AbstractResponse
     * @throws InvalidResponseException
     * @throws RateLimitedException
     * @throws UnsupportedResponseTypeException
     * @throws GuzzleException
     */
    final protected function execute(string $endpoint, array $params, string $method="GET"): AbstractResponse {

        $response = $this->client->request($method,self::ApiHost.$endpoint, [
            "query" => $params,
            "headers" => $this->getHeaders()
        ]);

        if ($response->getHeader("Ratelimit-Limit")) {
            $this->rateLimit = (int)$response->getHeader("Ratelimit-Limit")[0];
        }

        if ($response->getHeader("Ratelimit-Remaining")) {
            $this->rateLimitRemaining = (int)$response->getHeader("Ratelimit-Remaining")[0];
        }

        if ($response->getStatusCode() == 429) {
            throw new RateLimitedException();
        }

        $json = json_decode($response->getBody(), true);
        if ($json === false) {
            throw new InvalidResponseException();
        }

        if (isset($json['error'])) {
            return new ErrorResponse($response->getHeaders(), $json);
        }

        if (isset($json['data'])) {
            return new DataResponse($response->getHeaders(), $json);
        }

        throw new UnsupportedResponseTypeException();
    }

    public function getRateLimit(): ?int {
        return $this->rateLimit;
    }

    public function getRateLimitRemaining(): ?int {
        return $this->rateLimitRemaining;
    }
}
