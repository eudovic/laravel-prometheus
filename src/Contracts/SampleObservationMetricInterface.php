<?php

namespace Eudovic\PrometheusPHP\Contracts;

interface SampleObservationMetricInterface
{
    public function generateMetricsOutput(string $metricKey, string $key, array $values): array;

    public function calculateQuantiles(array $values, array $quantiles): array;

}
