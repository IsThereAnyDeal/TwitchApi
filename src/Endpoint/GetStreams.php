<?php
namespace IsThereAnyDeal\Twitch\Api\Endpoint;

use Generator;
use GuzzleHttp\Client;
use IsThereAnyDeal\Twitch\Api\Credentials;

class GetStreams extends AbstractPaginableEndpoint {

    private const Method = "GET";
    private const Endpoint = "streams";

    private array $skippedChannelsMap = [];

    public function __construct(Credentials $credentials, Client $client) {
        parent::__construct($credentials, $client, self::Endpoint, self::Method);
        $this->setLanguage("en");
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
    public function setUserLogin(string $userLogin): self {
        $this->setParam("user_login", $userLogin);
        return $this;
    }

    /** @return static */
    public function setChannelFilter(array $skipChannels): self {
        $this->skippedChannelsMap = array_flip($skipChannels);
        return $this;
    }

    public function getItemEnumerator(int $limit = -1): Generator {
        $enumerator = parent::getItemEnumerator($limit);
        foreach($enumerator as $item) {
            $username = $item['user_name'];

            if (isset($this->skippedChannelsMap[$username])) {
                continue;
            }

            yield $item;
        }
    }
}
