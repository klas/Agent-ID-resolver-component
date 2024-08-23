<?php

namespace App\Strategy;

use App\Models\Makler;
use App\Strategy\VnrResolvingStrategyInterface;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{

    public function resolve(array $data = []): ?Makler
    {
        // TODO: Implement resolve() method.
    }
}
