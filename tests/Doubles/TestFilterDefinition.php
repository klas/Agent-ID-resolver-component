<?php

namespace Tests\Doubles;

use App\Builder\StepFilterBuilderInterface;
use App\CoR\FilterDefinitions\FilterDefinitionInterface;
use Illuminate\Support\Str;

class TestFilterDefinition implements FilterDefinitionInterface
{
    protected StepFilterBuilderInterface $stepFilterBuilder;

    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder)
    {
        $this->stepFilterBuilder = &$stepFilterBuilder;
    }

    public function responsible(string $name): bool
    {
        return (string) Str::of(Str::lower($name))->snake() === 'test_company';
    }

    public function runFilterChain(): void
    {
        $this->stepFilterBuilder->filterNonNumeric();
    }
}
