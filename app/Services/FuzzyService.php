<?php

namespace App\Services;

use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;

class FuzzyService implements FuzzyInterface
{
    public function levenshtein(string $string1, string $string2): int
    {
        return levenshtein($string1, $string2);
    }

    public function textSimilarity(string $string1, string $string2, &$percent): float
    {
        return similar_text($string1, $string2, $percent);
    }

    public function fuzzyWuzzy(string $string1, string $string2): int
    {
        $fuzz = new Fuzz;
        $process = new Process($fuzz);

        return $fuzz->partialRatio($string1, $string2);
    }

    public function deleteOperationsScoreBothSides($source, $target): int
    {
        $sourceLen = strlen($source);
        $targetLen = strlen($target);

        // Create a 2D array to store the lengths of the longest common subsequence.
        $dp = array_fill(0, $sourceLen + 1, array_fill(0, $targetLen + 1, 0));

        // Fill the dp array based on the LCS algorithm.
        for ($i = 1; $i <= $sourceLen; $i++) {
            for ($j = 1; $j <= $targetLen; $j++) {
                if ($source[$i - 1] == $target[$j - 1]) {
                    // If characters match, increase the LCS length by 1.
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    // If they don't match, take the maximum LCS length of the previous states.
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }

        // The length of the longest common subsequence (LCS).
        $lcsLength = $dp[$sourceLen][$targetLen];

        // The minimum number of deletions needed on both sides.
        // Total deletions = (chars to delete from source) + (chars to delete from target)
        // = (source length - LCS length) + (target length - LCS length)
        $deletions = ($sourceLen - $lcsLength) + ($targetLen - $lcsLength);

        return $deletions;
    }
}
