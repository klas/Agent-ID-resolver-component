<?php

namespace App\Strategy;

use App\Models\Makler;

interface VnrResolvingStrategyInterface {
    public function resolve(array $data = []): ?Makler;
}
