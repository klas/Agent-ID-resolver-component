<?php

namespace App\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;
use Illuminate\Support\Collection;

interface FilterDefinitionInterface {
    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder);

    public function responsible(string $name): bool;

    public function runFilterChain(): void;

}
