<?php

namespace App\CoR\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;
use Illuminate\Support\Str;

class LiabilityInsuranceMagenstadtFilterDefinition implements FilterDefinitionInterface
{
    protected StepFilterBuilderInterface $stepFilterBuilder;

    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder)
    {
        $this->stepFilterBuilder = &$stepFilterBuilder;
    }

    public function responsible(string $name): bool
    {
        return (string) Str::of(Str::lower($name))->snake() === 'liability_insurance_magenstadt';
    }

    public function runFilterChain(): void
    {
        $this->stepFilterBuilder->filterPrefixChars('0')->filterNonNumeric();
    }
}
