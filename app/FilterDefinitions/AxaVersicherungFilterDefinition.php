<?php

namespace App\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;
use App\FilterDefinitions\FilterDefinitionInterface;
use Illuminate\Support\Collection;

class AxaVersicherungFilterDefinition implements FilterDefinitionInterface
{

    protected StepFilterBuilderInterface $stepFilterBuilder;

    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder)
    {
        $this->stepFilterBuilder = $stepFilterBuilder;
    }

    public function responsible(string $name): bool
    {
        return $name === 'Axa Versicherung';
    }

    public function runFilterChain(): void
    {
        $this->stepFilterBuilder->filterPrefixChars('9')->filterNonNumeric()->filterSuffixChars('0');
    }
}
