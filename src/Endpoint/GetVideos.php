<?php
namespace IsThereAnyDeal\Twitch\Api\Endpoint;

use GuzzleHttp\Client;
use IsThereAnyDeal\Twitch\Api\Credentials;

class GetVideos extends AbstractPaginableEndpoint {

    private const Method = "GET";
    private const Endpoint = "videos";

    public function __construct(Credentials $credentials, Client $client) {
        parent::__construct($credentials, $client, self::Endpoint, self::Method);

        $this->setLanguage("en");
        $this->setSort("trending");
    }

    public function setGameId(int $twitchGameId): self {
        $this->setParam("game_id", $twitchGameId);
        return $this;
    }

    public function setLanguage(string $language): self {
        $this->setParam("language", $language);
        return $this;
    }

    public function setPeriod(string $period): self {
        $this->setParam("period", $period);
        return $this;
    }

    public function setSort(string $sort): self {
        $this->setParam("sort", $sort);
        return $this;
    }

    public function setType(string $type): self {
        $this->setParam("type", $type);
        return $this;
    }
}
