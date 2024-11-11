<?php

namespace Tests\Unit;

use App\Builder\IdMatcherStepFilterBuilder;
use App\Services\FuzzyService;
use App\Strategy\VnrFuzzyResolvingStrategy;
use Tests\TestCase;

class VnrFuzzyResolvingStrategyTest extends TestCase
{
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = new VnrFuzzyResolvingStrategy(new IdMatcherStepFilterBuilder, new FuzzyService);
    }

}
