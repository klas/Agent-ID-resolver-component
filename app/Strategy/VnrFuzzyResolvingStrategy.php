<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\MaklerDTO;
use App\Models\Vnralias;
use App\Services\FuzzyInterface;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{
    protected const EXPECTED_VNR_LENGTH = 6;

    use VnrResolvingStrategyHelper;

    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder, protected FuzzyInterface $fuzzy) {}

    public function resolve(array $data = []): ?MaklerDTO
    {
        // Try to match with stored aliases
        $makler = $this->getMaklerPerExactVnr($data['gesellschaft'], $data['vnr']);

        $debug = [];

        if (! $makler) {
            $searchableVnrAliases = $this->getSearchableVnrAliases($data['gesellschaft']);

            $searchableVnrAliases->each(function ($alias) use (&$makler, $data, &$debug) {

                // First remove obvious noise

                $var1 = $this->stepFilterBuilder->setFilterable($alias->name)->filterPrefixChars('9')->filterNonAlphaNumeric()->getFiltered();
                $var2 = $this->stepFilterBuilder->setFilterable($data['vnr'])->filterPrefixChars('9')->filterNonAlphaNumeric()->getFiltered();

                //$var1 = $alias->name;
                //$var2 = $data['vnr'];

                $var1Len = strlen($var1);
                $var2Len = strlen($var2);

                if ($var1Len < $var2Len) {
                    $shorterVarLen = $var1Len;
                    $shorterVar = $var1;
                    $longerVarLen = $var2Len;
                    $longerVar = $var2;
                } else {
                    $shorterVarLen = $var2Len;
                    $shorterVar = $var2;
                    $longerVarLen = $var1Len;
                    $longerVar = $var1;
                }

                //Now try to fuzzy match
                $levScore = $this->fuzzy->levenshtein($shorterVar, $longerVar);
                $similarityScore = $this->fuzzy->textSimilarity($shorterVar, $longerVar, $percent);
                $fuzzy = $this->fuzzy->fuzzyWuzzy($shorterVar, $longerVar);
                $diff = $this->stringDiff($shorterVar, $longerVar);
                $match = $fuzzy == 100 || ($fuzzy >= 87 && ! preg_match('~[1-9]+~', $diff));
                $debug[] = [
                    [$shorterVar, $longerVar],
                    'alias' => $alias->name,
                    'lev score' => $levScore,
                    'similarity score' => [$similarityScore, $percent],
                    //'intersection' => $this->intersection($alias->name, $data['vnr']),
                    //'lenghts' => [$shorterVarLen, $longerVarLen],
                    //'delete score both sides' => $this->fuzzy->deleteOperationsScoreBothSides($shorterVar, $longerVar),
                    //'delete score' => [$this->delete_operations_score($shorterVar, $longerVar), $this->delete_operations_score($longerVar, $shorterVar)],
                    'fuzzy' => $this->fuzzy->fuzzyWuzzy($shorterVar, $longerVar),
                    'diff' => $diff,
                    'match' => $match,
                ];
                dump($debug);
                if ($match) {
                    $makler = $alias?->gesellschafts_makler->makler;
                    //Vnralias::create(['name' => $data['vnr'], 'gm_id' => $alias->gm_id]);



                    return false; // break loop
                }

            });

        }
        $debug[] = [
            [$data['gesellschaft'], $data['vnr'], $makler?->name],
        ];

        dump($debug);
        if ($makler) {
            return new MaklerDTO($makler->name);
        }

        return null;
    }

    public function intersection(string $string1, string $string2): string
    {
        //return implode( '' , array_intersect( str_split($string1) , str_split($string2) ) );

        return implode('',
            call_user_func_array('array_intersect',
                array_map(function ($a) {
                    return str_split($a);
                },
                    [$string1, $string2])
            )
        );
    }

    public function differentChars(string $string1, string $string2): string
    {
        $arr1 = str_split($string1);
        $arr2 = str_split($string2);

        return implode('', array_merge(array_diff($arr1, $arr2), array_diff($arr2, $arr1)));
    }

    public function string_diff_old($str1, $str2)
    {
        $diff = '';
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        $maxLen = max($len1, $len2);

        for ($i = 0; $i < $maxLen; $i++) {
            if ($i >= $len1) {
                $diff .= $str2[$i];
            } elseif ($i >= $len2) {
                $diff .= $str1[$i];
            } elseif ($str1[$i] !== $str2[$i]) {
                $diff .= $str2[$i];
            }
        }

        return $diff;
    }

    public function delete_operations_score($source, $target)
    {
        $sourceLen = strlen($source);
        $targetLen = strlen($target);

        // Create a 2D array to store the delete operations score.
        $dp = array_fill(0, $sourceLen + 1, array_fill(0, $targetLen + 1, 0));

        // If target is empty, delete all characters from the source.
        for ($i = 1; $i <= $sourceLen; $i++) {
            $dp[$i][0] = $i;
        }

        // If source is empty, we don't need to delete any characters (no cost).
        for ($j = 1; $j <= $targetLen; $j++) {
            $dp[0][$j] = 0;
        }

        // Fill the dp array by checking characters one by one.
        for ($i = 1; $i <= $sourceLen; $i++) {
            for ($j = 1; $j <= $targetLen; $j++) {
                if ($source[$i - 1] == $target[$j - 1]) {
                    // If characters match, no delete needed (carry over previous state).
                    $dp[$i][$j] = $dp[$i - 1][$j - 1];
                } else {
                    // If characters don't match, we delete from the source string.
                    $dp[$i][$j] = $dp[$i - 1][$j] + 1;
                }
            }
        }

        // The score will be the minimum number of deletions required to match.
        return $dp[$sourceLen][$targetLen];
    }

    public function stringDiff($str1, $str2)
    {
        $ret = '';

        $diff = $this->calculateDiff(str_split($str1), str_split($str2));

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
            $this->calculateDiff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }
}
