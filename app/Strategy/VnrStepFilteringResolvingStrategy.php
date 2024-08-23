<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\Models\Makler;
use App\Strategy\VnrResolvingStrategyInterface;

class VnrStepFilteringResolvingStrategy implements VnrResolvingStrategyInterface
{
    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder) {}

    public function resolve(array $data = []): ?Makler
    {
        return null;
    }
}
