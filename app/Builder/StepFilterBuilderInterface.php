<?php

namespace App\Builder;

interface StepFilterBuilderInterface {
    public function setData(array $data): self;

    public function filterNonAscii(string $filterable): self;

    public function filterPrefixZeroes(string $filterable): self;

    public function filterPrefixNinetyNine(string $filterable): self;

    public function filterPrefixZeroes(string $filterable): self;
}
