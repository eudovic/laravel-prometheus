<?php

namespace Eudovic\PrometheusPHP\Metrics\Types;

use Eudovic\PrometheusPHP\Abstracts\AbstractMetric;
use Eudovic\PrometheusPHP\Models\Message;

class Summary extends AbstractMetric
{
    const METRIC_TYPE = 'summary';
    
    public static function addMetric(string $name, array $metrics, string $label)
    {

        $instance = new self();
        $messages = array_map(function ($metric) {
            $message = new Message();
            $message->setMessage($metric['key'], $metric['params'], $metric['value']);
            return $message;
        }, $metrics);

        return $instance->metric(self::METRIC_TYPE, $name, $label, $messages);
    }
}
