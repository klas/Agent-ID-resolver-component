<?php

namespace App\Builder;

interface StepFilterBuilderInterface
{
    public function setFilterable(string $filterable): self;

    public function getFiltered(): ?string;

    public function filterNonAlphaNumeric(): self;

    public function filterNonNumeric(): self;

    public function filterPrefixChars(string $char): self;

    public function filterSuffixChars(string $char): self;
}
