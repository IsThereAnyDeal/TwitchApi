<?php
namespace IsThereAnyDeal\Twitch\Api\Endpoint;

use Generator;
use GuzzleHttp\Client;
use IsThereAnyDeal\Twitch\Api\Credentials;
use IsThereAnyDeal\Twitch\Api\Exception\TwitchApiException;
use IsThereAnyDeal\Twitch\Api\Response\DataResponse;

abstract class AbstractPaginableEndpoint extends AbstractEndpoint {

    private string $endpoint;
    private string $method;

    private ?string $cursor = null;
    private bool $reachedEnd = false;

    private array $params = [];

    protected function __construct(Credentials $credentials, Client $client, string $endpoint, string $method) {
        parent::__construct($credentials, $client);
        $this->endpoint = $endpoint;
        $this->method = $method;
    }

    final protected function setParam(string $param, $value): void {
        $this->params[$param] = $value;
    }

    final protected function getParam(string $param) {
        return $this->params[$param];
    }

    private function getParams(): array {
        $params = $this->params;

        if (!is_null($this->cursor)) {
            $params['after'] = $this->cursor;
        }

        return $params;
    }

    public function setFirst(int $size): self {
        $this->setParam("first", $size);
        return $this;
    }

    public function getNextPage(): array {

        if ($this->reachedEnd) {
            return [];
        }

        try {
            $response = $this->execute($this->endpoint, $this->getParams(), $this->method);
        } catch (TwitchApiException $e) {
            return [];
        }

        if (!($response instanceof DataResponse)) {
            return [];
        }

        if ($response->hasCursor()) {
            $this->cursor = $response->getCursor();
        } else {
            $this->reachedEnd = true;
        }

        return $response->getData();
    }

    public function getAllPages(): array {
        $result = [];
        $data = $this->getNextPage();

        while (!empty($data)) {
            $result = array_merge($result, $data);
            $data = $this->getNextPage();
        }
        return $result;
    }

    public function getPageEnumerator(): iterable {
        $data = $this->getNextPage();

        while (!empty($data)) {
            yield $data;
            $data = $this->getNextPage();
        }
    }

    public function getItemEnumerator(int $limit = -1): Generator {
        $data = $this->getNextPage();

        $items = 0;
        while (!empty($data)) {
            foreach($data as $item) {
                yield $item;
                if ($limit > 0 && ++$items >= $limit) { break 2; }
            }
            $data = $this->getNextPage();
        }
    }
}
