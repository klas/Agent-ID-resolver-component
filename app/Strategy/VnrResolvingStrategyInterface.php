<?php

namespace App\Strategy;

use App\DTO\MaklerDTO;

interface VnrResolvingStrategyInterface
{
    public function resolve(array $data = []): ?MaklerDTO;
}
