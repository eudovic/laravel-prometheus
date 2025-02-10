<?php

namespace Eudovic\PrometheusPHP\Contracts;

interface MessageInterface
{
    public function setMessage(string $key, array $params, string $value): void;

    public function getMessage(): string;
}
