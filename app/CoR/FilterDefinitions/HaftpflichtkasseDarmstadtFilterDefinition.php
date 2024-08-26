<?php

namespace App\CoR\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;
use Illuminate\Support\Str;

class HaftpflichtkasseDarmstadtFilterDefinition implements FilterDefinitionInterface
{

    protected StepFilterBuilderInterface $stepFilterBuilder;

    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder)
    {
        $this->stepFilterBuilder = &$stepFilterBuilder;
    }

    public function responsible(string $name): bool
    {
        return (string)Str::of($name)->snake() === 'haftpflichtkasse_darmstadt';
    }

    public function runFilterChain(): void
    {
        $this->stepFilterBuilder->filterPrefixChars('0')->filterNonNumeric();
    }
}
