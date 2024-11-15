<?php

namespace App\Strategy;

use App\DTO\AgentDTO;

interface AidResolvingStrategyInterface
{
    public function resolve(array $data = []): ?AgentDTO;
}
