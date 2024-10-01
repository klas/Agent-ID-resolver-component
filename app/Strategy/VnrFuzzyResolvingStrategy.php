<?php

namespace App\Strategy;

use App\DTO\MaklerDTO;
use App\Models\Gesellschaft;
use App\Models\Vnralias;
use App\Services\FuzzyInterface;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{
    protected const EXPECTED_VNR_LENGTH = 6;

    public function __construct(protected FuzzyInterface $fuzzy) {}

    public function resolve(array $data = []): ?MaklerDTO
    {
        $gesellschaft = Gesellschaft::whereName($data['gesellschaft'])->with('maklers')->firstOrFail();

        // Try to match with stored aliases
        $searchableAliases = collect([]);

        $gesellschaft->maklers->each(function ($makler) use (&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($makler->pivot->vnraliases);
        });

        $debug = [];

        $searchableAliases->each(function ($alias) use (&$makler, $data, &$debug) {



            $var1Len = strlen($alias->name);
            $var2Len = strlen($data['vnr']);

            if ($var1Len < $var2Len) {
                $shorterVarLen = $var1Len;
                $shorterVar = $alias->name;
                $longerVarLen = $var2Len;
                $longerVar = $data['vnr'];
            } else {
                $shorterVarLen = $var2Len;
                $shorterVar = $data['vnr'];
                $longerVarLen = $var1Len;
                $longerVar = $alias->name;
            }

            //try fuzzy match
            $levScore = $this->fuzzy->levenshtein($shorterVar, $longerVar);
            $levScore2 = $this->fuzzy->levenshtein($longerVar, $shorterVar);

            $similarityScore = $this->fuzzy->textSimilarity($shorterVar, $longerVar, $percent);

            $differenceChars = $shorterVarLen - self::EXPECTED_VNR_LENGTH;
            //$match = $similarityScore >= $shorterVarLen && $similarityScore >= self::EXPECTED_VNR_LENGTH;
            //$match = $similarityScore >= ($shorterVarLen - $this->delete_operations_score($shorterVar, $longerVar)) && $similarityScore >= self::EXPECTED_VNR_LENGTH;
            $match = ($similarityScore - $differenceChars) >= $shorterVarLen && $similarityScore >= self::EXPECTED_VNR_LENGTH;
            //

            //$match = $similarityScore - $shorterVarLen - $this->delete_operations_score($shorterVar, $longerVar)) && $similarityScore >= self::EXPECTED_VNR_LENGTH;

            $debug[] = [[$alias->name, $data['vnr']],
                'lev score' => [$levScore, $levScore2],
                'similarity score' => [$similarityScore, $percent],
                //'intersection' => $this->intersection($alias->name, $data['vnr']),
                'lenghts' => [$shorterVarLen, $longerVarLen],
                'delete score both sides' => $this->fuzzy->deleteOperationsScoreBothSides($shorterVar, $longerVar),
                /*'delete score' => [$this->delete_operations_score($shorterVar, $longerVar),
                    $this->delete_operations_score($longerVar, $shorterVar)],*/
                //$this->matchingCharactersScore($alias->name, $data['vnr']),
                'fuzzy' => $this->fuzzy->fuzzyWuzzy($shorterVar, $longerVar),
                'match' => $match,
            ];

            if (false && $matchScore <= 100) {
                $makler = $alias?->gesellschafts_makler->makler;
                Vnralias::create(['name' => $data['vnr'], 'gm_id' => $alias->gm_id]);

                return false; // break loop
            }

            dump($debug);
        });

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

}
