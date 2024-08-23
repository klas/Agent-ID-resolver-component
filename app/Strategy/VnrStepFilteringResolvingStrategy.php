<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\MaklerDTO;
use App\Strategy\VnrResolvingStrategyInterface;

class VnrStepFilteringResolvingStrategy implements VnrResolvingStrategyInterface
{
    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder) {}

    public function resolve(array $data = []): ?MaklerDTO
    {
        return null;
    }
}
