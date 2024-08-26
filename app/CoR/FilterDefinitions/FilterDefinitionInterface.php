<?php

namespace App\CoR\FilterDefinitions;

use App\Builder\StepFilterBuilderInterface;

interface FilterDefinitionInterface {
    public function setStepFilterBuilder(StepFilterBuilderInterface &$stepFilterBuilder);

    public function responsible(string $name): bool;

    public function runFilterChain(): void;

}
