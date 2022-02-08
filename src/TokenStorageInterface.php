<?php
namespace IsThereAnyDeal\Twitch\Api;

interface TokenStorageInterface {
    function get(): ?Token;
    function set(Token $token, int $expiry): void;
}
