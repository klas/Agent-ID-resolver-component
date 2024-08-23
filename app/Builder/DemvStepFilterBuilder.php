<?php

namespace App\Builder;

use App\Builder\StepFilterBuilderInterface;

class DemvStepFilterBuilder implements StepFilterBuilderInterface
{
    protected array $data;

    public function setData(array $data): StepFilterBuilderInterface
    {
        $this->data = $data;
    }

    public function filterNonAscii(string $filterable): StepFilterBuilderInterface
    {
        // TODO: Implement filterNonAscii() method.
    }

    public function filterPrefixZeroes(string $filterable): StepFilterBuilderInterface
    {
        // TODO: Implement filterPrefixZeroes() method.
    }

    public function filterPrefixNinetyNine(string $filterable): StepFilterBuilderInterface
    {
        // TODO: Implement filterPrefixNinetyNine() method.
    }
}
