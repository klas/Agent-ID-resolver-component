<?php

namespace App\Services;

interface MatchingInterface
{
    public function match(string $string1, string $string2): bool;
}
