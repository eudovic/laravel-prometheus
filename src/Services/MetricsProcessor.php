<?php

namespace Eudovic\PrometheusPHP\Services;

class MetricsProcessor
{
    public static function process(string $metricKey, array $payload): array
    {
        if (empty($payload)) {
            return [];
        }

        $quantiles = QuantileCalculator::calculate($payload, [0.5, 0.9, 0.99]);
        $sum = array_sum(array_column($payload, 'value'));
        $count = count($payload);
        $params = $payload[0]['params'] ?? [];
       

        $output = [];
        foreach ($quantiles as $quantile) {
            $output[] = [
                'key' => $metricKey,
                'params' => array_merge([
                    'quantile' => $quantile['quantile'],
                ], $params),
                'value' => $quantile['value']['value'],
            ];
        }
        
        $output[] = [
            'key' => "{$metricKey}_sum",
            'params' =>  $params,
            'value' => $sum,
        ];
        
        $output[] = [
            'key' => "{$metricKey}_count",
            'params' =>  $params,
            'value' => $count,
        ];

        return $output;
    }
}
