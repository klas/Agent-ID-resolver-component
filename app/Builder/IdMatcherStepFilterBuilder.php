<?php

namespace App\Builder;

class IdMatcherStepFilterBuilder implements StepFilterBuilderInterface
{
    protected ?string $filterable = null;

    public function setFilterable(string $filterable): StepFilterBuilderInterface
    {
        $this->filterable = $filterable;

        return $this;
    }

    public function getFiltered(): ?string
    {
        return $this->filterable;
    }

    public function filterNonAlphaNumeric(): StepFilterBuilderInterface
    {
        $this->filterable = preg_replace('/[^A-Za-z0-9]/', '', $this->filterable);

        return $this;
    }

    public function filterNonNumeric(): StepFilterBuilderInterface
    {
        $this->filterable = preg_replace('/[^0-9]/', '', $this->filterable);

        return $this;
    }

    public function filterPrefixChars(string $char): StepFilterBuilderInterface
    {
        $this->filterable = ltrim($this->filterable, $char);

        return $this;
    }

    public function filterSuffixChars(string $char): StepFilterBuilderInterface
    {
        $this->filterable = rtrim($this->filterable, $char);

        return $this;
    }
}
