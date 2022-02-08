<?php
namespace IsThereAnyDeal\Twitch\Api\Endpoint;

use GuzzleHttp\Client;
use IsThereAnyDeal\Twitch\Api\Credentials;

class GetTopGames extends AbstractPaginableEndpoint {

    private const Method = "GET";
    private const Endpoint = "games/top";

    public function __construct(Credentials $credentials, Client $client) {
        parent::__construct($credentials, $client, self::Endpoint, self::Method);
    }
}
