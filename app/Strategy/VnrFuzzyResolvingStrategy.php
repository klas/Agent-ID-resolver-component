<?php

namespace App\Strategy;

use App\DTO\MaklerDTO;
use App\Strategy\VnrResolvingStrategyInterface;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{

    public function resolve(array $data = []): ?MaklerDTO
    {
        // TODO: Implement resolve() method.
    }
}
