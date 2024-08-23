<?php

namespace App\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;
use App\FilterDefinitions\FilterDefinitionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DieBayerischeFilterDefinition implements FilterDefinitionInterface
{

    protected StepFilterBuilderInterface $stepFilterBuilder;

    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder)
    {
        $this->stepFilterBuilder = $stepFilterBuilder;
    }

    public function responsible(string $name): bool
    {
        return (string)Str::of($name)->snake() === 'die_bayerische';
    }

    public function runFilterChain(): void
    {
        $this->stepFilterBuilder->filterNonNumeric();
    }
}
