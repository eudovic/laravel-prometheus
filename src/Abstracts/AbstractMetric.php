<?php

namespace Eudovic\PrometheusPHP\Abstracts;

use Eudovic\PrometheusPHP\Contracts\MetricInterface;
use Eudovic\PrometheusPHP\Contracts\SampleObservationMetricInterface;
use Eudovic\PrometheusPHP\Models\Message;

abstract class AbstractMetric implements MetricInterface, SampleObservationMetricInterface
{

    protected $type;
    protected $key;
    protected $label;
    protected $values;

    public const METRIC_TYPE_COUNTER = 'counter';
    public const METRIC_TYPE_GAUGE = 'gauge';
    public const METRIC_TYPE_SUMMARY = 'summary';
    public const METRIC_TYPE_HISTOGRAM = 'histogram';


    public function metric(string $type, string $key, string $label, array $messages)
    {
        $this->type = $type;
        $this->key = $key;
        $this->label = $label;
        $this->values = $this->validateMessages($messages);
        return $this->formatMetric($type, $key, $label, $messages);
    }

    public function validateType(string $type): string
    {
        $types = [
            self::METRIC_TYPE_COUNTER,
            self::METRIC_TYPE_GAUGE,
            self::METRIC_TYPE_SUMMARY,
            self::METRIC_TYPE_HISTOGRAM
        ];

        if (!in_array($type, $types)) {
            throw new \InvalidArgumentException('Invalid metric type.');
        }

        return $type;
    }

    /**
     * Valida que $messages é um array de Message.
     *
     * @param array $messages
     * @return array<Message>
     */
    public function validateMessages(array $messages): array
    {
        foreach ($messages as $message) {
            if (!$message instanceof Message) {
                throw new \InvalidArgumentException('Each item in $messages must be an instance of Message.');
            }
        }

        return $messages;
    }

    public function formatMetric(string $type, string $key, string $label, array $messages): string
    {
        $metricString = $this->header($key, $label, $type);
        foreach ($messages as $message) {
            $metricString .= $message->getMessage();
        }

        return $metricString;
    }



    protected function header($key, $label, $type)
    {
        $metricString = "# HELP {$key} {$label}\n";
        $metricString .= "# TYPE {$key} {$type}\n";
        return $metricString;
    }


    public function generateMetricsOutput(string $metricKey, string $key, array $values): array
    {
        if (empty($values)) {
            return [];
        }

        $quantiles = $this->calculateQuantiles($values, [0.5, 0.9, 0.99]);
        $sum = array_sum($values);
        $count = count($values);

        $output = [];
        foreach ($quantiles as $quantile) {
            $output[] = sprintf(
                "%s{key=\"%s\", quantile=\"%s\"} %s",
                $metricKey,
                $key,
                $quantile['quantile'],
                $quantile['value']
            );
        }

        $output[] = sprintf("%s_sum{key=\"%s\"} %s", $metricKey, $key, $sum);
        $output[] = sprintf("%s_count{key=\"%s\"} %s", $metricKey, $key, $count);

        return $output;
    }

    public function calculateQuantiles(array $values, array $quantiles): array
    {
        sort($values);
        $results = [];
        $count = count($values);

        foreach ($quantiles as $quantile) {
            $index = (int) floor($quantile * ($count - 1));
            $results[] = ['quantile' => $quantile, 'value' => $values[$index]];
        }

        return $results;
    }
}
