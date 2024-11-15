<?php

namespace Tests\Unit;

use App\Builder\IdMatcherStepFilterBuilder;
use App\Services\FuzzyService;
use App\Strategy\AidFuzzyResolvingStrategy;
use Tests\TestCase;

class AidFuzzyResolvingStrategyTest extends TestCase
{
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = new AidFuzzyResolvingStrategy(new IdMatcherStepFilterBuilder, new FuzzyService);
    }

}
