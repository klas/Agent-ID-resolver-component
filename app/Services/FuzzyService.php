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

    public function stringDiff($str1, $str2): string
    {
        $ret = '';

        $diff = $this->calculateDiff(str_split($str1), str_split($str2));

        // Counting deletes and inserts, could be modified to use just one
        foreach ($diff as $k) {
            if (is_array($k)) {
                $ret .= (! empty($k['d']) ? implode('', $k['d']) : '').
                    (! empty($k['i']) ? implode('', $k['i']) : '');
            }
        }

        return $ret;

    }

    protected function calculateDiff($old, $new)
    {
        $matrix = [];
        $maxlen = 0;

        foreach ($old as $oindex => $ovalue) {
            $nkeys = array_keys($new, $ovalue);
            foreach ($nkeys as $nindex) {
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if ($matrix[$oindex][$nindex] > $maxlen) {
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }
        }

        if ($maxlen == 0) {
            return [['d' => $old, 'i' => $new]];
        }

        return array_merge(
            $this->calculateDiff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            $this->calculateDiff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen))
        );
    }
}
