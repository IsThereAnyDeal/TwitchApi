<?php
namespace IsThereAnyDeal\Twitch\Api\Endpoint;

use GuzzleHttp\Client;
use IsThereAnyDeal\Twitch\Api\Credentials;

class GetGames extends AbstractPaginableEndpoint {

    private const Endpoint = "games";
    private const Method = "GET";

    public function __construct(Credentials $credentials, Client $client) {
        parent::__construct($credentials, $client, self::Endpoint, self::Method);
    }

    /** @return static */
    public function setGameId(int $twitchGameId): self {
        $this->setParam("id", $twitchGameId);
        return $this;
    }
}
