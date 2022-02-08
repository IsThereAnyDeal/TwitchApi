<?php
namespace IsThereAnyDeal\Twitch\Api;

class Token {

    private string $token;

    public function __construct(string $token) {
        $this->token = $token;
    }

    public function getValue(): string {
        return $this->token;
    }

    public function __toString(): string {
        return $this->token;
    }
}
