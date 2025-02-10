<?php

namespace Eudovic\PrometheusPHP\Models;

use Eudovic\PrometheusPHP\Contracts\MessageInterface;

class Message implements MessageInterface
{

    protected $key;

    protected $params;

    protected $value;


    public function setMessage(string $key, array $params, string $value): void
    {
        $this->key = $key;
        $this->params = $params;
        $this->value = $value;
    }

    public function getMessage(): string
    {
        return $this->formatMessage($this->key, $this->params, $this->value);
    }

    public function formatMessage(string $key, array $params, string $value): string
    {
        $messageString = $this->key($key);
        $messageString .= $this->params($params);
        $messageString .= " ".$this->value($value);

        return $messageString;
    }

    protected function key($key)
    {
        return "{$key}";
    }

    protected static function params(array $params): string
    {
        if (empty($params)) {
            return '';
        }
    
        $formattedParams = [];
        foreach ($params as $key => $value) {
            $formattedParams[] = sprintf('%s="%s"', $key, $value);
        }
    
        return '{' . implode(', ', $formattedParams) . '}';
    }

    protected function value($value)
    {
        return " {$value}\n";
    }
}
