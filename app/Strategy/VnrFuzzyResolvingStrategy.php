<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\MaklerDTO;
use App\Models\Vnralias;
use App\Services\FuzzyInterface;
use InvalidArgumentException;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{

    use VnrResolvingStrategyHelper;

    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder, protected FuzzyInterface $fuzzy) {}

    public function resolve(array $data = []): ?MaklerDTO
    {
        if (! isset($data['gesellschaft']) || ! isset($data['vnr'])) {
            throw new InvalidArgumentException;
        }

        // Try to match with stored aliases
        $makler = $this->getMaklerPerExactVnr($data['gesellschaft'], $data['vnr']);

        $debug = [];

        if (! $makler) {
            $searchableVnrAliases = $this->getSearchableVnrAliases($data['gesellschaft']);

            $searchableVnrAliases->each(function ($alias) use (&$makler, $data, &$debug) {

                // First remove obvious noise
                $var1 = $this->stepFilterBuilder->setFilterable($alias->name)->filterPrefixChars('9')->filterNonAlphaNumeric()->getFiltered();
                $var2 = $this->stepFilterBuilder->setFilterable($data['vnr'])->filterPrefixChars('9')->filterNonAlphaNumeric()->getFiltered();

                $var1Len = strlen($var1);
                $var2Len = strlen($var2);

                if ($var1Len < $var2Len) {
                    $shorterVar = $var1;
                    $longerVar = $var2;
                } else {
                    $shorterVar = $var2;
                    $longerVar = $var1;
                }

                //Now try to fuzzy match
                $similarityScore = $this->fuzzy->textSimilarity($shorterVar, $longerVar, $percent);
                $fuzzy = $this->fuzzy->fuzzyWuzzy($shorterVar, $longerVar);
                $diff = $this->fuzzy->stringDiff($shorterVar, $longerVar);

                $match = $fuzzy == 100 || ($fuzzy >= 87 && ! preg_match('~[1-9]+~', $diff));

                /*$debug[] = [
                    [$shorterVar, $longerVar],
                    'alias' => $alias->name,
                    //'lev score' => $this->fuzzy->levenshtein($shorterVar, $longerVar),
                    //'similarity score' => [$similarityScore, $percent],
                    'fuzzy' => $this->fuzzy->fuzzyWuzzy($shorterVar, $longerVar),
                    'diff' => $diff,
                    'match' => $match,
                ];
                dump($debug);*/

                if ($match) {
                    $makler = $alias?->gesellschafts_makler->makler;

                    // Store alias
                    Vnralias::create(['name' => $data['vnr'], 'gm_id' => $alias->gm_id]);

                    return false; // break loop
                }

            });

        }

        /*$debug[] = [
            [$data['gesellschaft'], $data['vnr'], $makler?->name],
        ];

        dump($debug);*/

        if ($makler) {
            return new MaklerDTO($makler->name);
        }

        return null;
    }
}
