<?php

namespace App\Services;

interface FuzzyInterface
{
    public function levenshtein(string $string1, string $string2): int;

    public function textSimilarity(string $string1, string $string2, &$percent): float;

    public function fuzzyWuzzy(string $string1, string $string2): int;

    public function deleteOperationsScoreBothSides($source, $target): int;

    public function stringDiff($str1, $str2): string;

}
