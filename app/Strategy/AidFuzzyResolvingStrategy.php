<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\AgentDTO;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use App\Services\FuzzyInterface;
use InvalidArgumentException;

class AidFuzzyResolvingStrategy implements AidResolvingStrategyInterface
{
    private const FUZZY_THRESHOLD = 87;

    public function __construct(
        protected StepFilterBuilderInterface $stepFilterBuilder,
        protected FuzzyInterface $fuzzy,
        protected AidAliasRepositoryInterface $aidAliasRepository
    ) {}

    public function resolve(array $data = []): ?AgentDTO
    {
        if (! isset($data['company']) || ! isset($data['aid'])) {
            throw new InvalidArgumentException;
        }

        // Try to match with stored aliases
        $agent = $this->aidAliasRepository->getAgentByExactAid($data['company'], $data['aid']);

        if (! $agent) {
            $searchableAidAliases = $this->aidAliasRepository->getSearchableAidAliases($data['company']);

            foreach ($searchableAidAliases as $alias) {
                // First remove obvious noise
                $var1 = $this->stepFilterBuilder
                    ->setFilterable($alias->name)
                    ->filterPrefixChars('9')
                    ->filterNonAlphaNumeric()
                    ->getFiltered();

                $var2 = $this->stepFilterBuilder
                    ->setFilterable($data['aid'])
                    ->filterPrefixChars('9')
                    ->filterNonAlphaNumeric()
                    ->getFiltered();

                $var1Len = strlen($var1);
                $var2Len = strlen($var2);

                if ($var1Len < $var2Len) {
                    $shorterVar = $var1;
                    $longerVar = $var2;
                } else {
                    $shorterVar = $var2;
                    $longerVar = $var1;
                }

                // Now try to fuzzy match
                $similarityScore = $this->fuzzy->textSimilarity($shorterVar, $longerVar, $percent);
                $fuzzy = $this->fuzzy->fuzzyWuzzy($shorterVar, $longerVar);
                $diff = $this->fuzzy->stringDiff($shorterVar, $longerVar);

                $match = $fuzzy == 100 || ($fuzzy >= self::FUZZY_THRESHOLD && ! preg_match('~[1-9]+~', $diff));

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
                    $agent = $alias->companies_agent->agent;

                    // Store alias using repository
                    $this->aidAliasRepository->create([
                        'name' => $data['aid'],
                        'gm_id' => $alias->gm_id,
                    ]);

                    break; // Exit loop
                }
            }
        }

        /*$debug[] = [
            [$data['company'], $data['aid'], $agent?->name],
        ];

        dump($debug);*/

        return $agent ? new AgentDTO($agent->name) : null;
    }
}
