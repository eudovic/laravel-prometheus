<?php

namespace Eudovic\PrometheusPHP\Contracts;

interface MetricInterface 
{
   
    function metric(string $type, string $key, string $label, array $messages);

    function validateType(string $type): string;

    function validateMessages(array $messages): array;

    function formatMetric(string $type, string $key, string $label, array $messages): string;
}
