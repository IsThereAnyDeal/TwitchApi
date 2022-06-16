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

    /** @return static */
    public function setGameId(int $twitchGameId): self {
        $this->setParam("game_id", $twitchGameId);
        return $this;
    }

    /** @return static */
    public function setLanguage(string $language): self {
        $this->setParam("language", $language);
        return $this;
    }

    /** @return static */
    public function setPeriod(string $period): self {
        $this->setParam("period", $period);
        return $this;
    }

    /** @return static */
    public function setSort(string $sort): self {
        $this->setParam("sort", $sort);
        return $this;
    }

    /** @return static */
    public function setType(string $type): self {
        $this->setParam("type", $type);
        return $this;
    }
}
