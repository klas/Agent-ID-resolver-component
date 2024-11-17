<?php

namespace App\CoR\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;
use Illuminate\Support\Str;

class MMAFilterDefinition implements FilterDefinitionInterface
{
    protected StepFilterBuilderInterface $stepFilterBuilder;

    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder)
    {
        $this->stepFilterBuilder = &$stepFilterBuilder;
    }

    public function responsible(string $name): bool
    {
        return (string) Str::of(Str::lower($name))->snake() === 'mma';
    }

    public function runFilterChain(): void
    {
        $this->stepFilterBuilder->filterPrefixChars('Q')->filterNonNumeric();
    }
}
