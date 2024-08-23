<?php

namespace App\Builder;

use App\Builder\StepFilterBuilderInterface;

class DemvStepFilterBuilder implements StepFilterBuilderInterface
{
    protected ?string $filterable = null;

    public function setFilterable(string $filterable): StepFilterBuilderInterface
    {
        $this->$filterable = $filterable;

        return $this;
    }

    public function getFiltered(): ?string
    {
        return $this->filterable;
    }

    public function filterNonAscii(): StepFilterBuilderInterface
    {
        // TODO: Implement filterNonAscii() method.

        return $this;
    }

    public function filterPrefixZeroes(): StepFilterBuilderInterface
    {
        // TODO: Implement filterPrefixZeroes() method.

        return $this;
    }

    public function filterPrefixNinetyNine(): StepFilterBuilderInterface
    {
        // TODO: Implement filterPrefixNinetyNine() method.

        return $this;
    }


}
