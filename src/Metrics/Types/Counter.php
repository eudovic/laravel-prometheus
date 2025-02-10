<?php

namespace Eudovic\PrometheusPHP\Metrics\Types;

use Eudovic\PrometheusPHP\Abstracts\AbstractMetric;
use Eudovic\PrometheusPHP\Models\Message;

class Counter extends AbstractMetric
{
    const METRIC_TYPE = 'counter';

    public static function addMetric(string $name, string|int $value, string $label)
    {
        $instance = new self();
        $message = new Message();
        $message->setMessage($name, [], $value);
        return $instance->metric(self::METRIC_TYPE, $name, $label, [$message]);
    }
}
