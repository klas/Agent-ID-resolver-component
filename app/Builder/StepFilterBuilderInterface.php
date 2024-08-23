<?php

namespace App\Builder;

interface StepFilterBuilderInterface {
    public function setFilterable(string $filterable): self;

    public function getFiltered(): ?string;

    public function filterNonAscii(): self;

    public function filterPrefixZeroes(): self;

    public function filterPrefixNinetyNine(): self;
}
