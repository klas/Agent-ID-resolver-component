<?php

namespace App\Strategy;

use App\DTO\MaklerDTO;
use App\Models\Gesellschaft;
use App\Models\Vnralias;
use App\Strategy\VnrResolvingStrategyInterface;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{

    public function resolve(array $data = []): ?MaklerDTO
    {
        // TODO: Implement resolve() method.
    }
}
