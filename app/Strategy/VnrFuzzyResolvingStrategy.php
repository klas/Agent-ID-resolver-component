<?php

namespace App\Strategy;

use App\DTO\MaklerDTO;
use App\Models\Gesellschaft;
use App\Models\Vnralias;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{
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

            //try fuzzy match
            $matchScore = levenshtein($alias->name, $data['vnr'], 100, 100, 1);
            $matchScore2 = levenshtein($data['vnr'], $alias->name, 100, 100, 1);
            $similarityScore = similar_text($alias->name, $data['vnr']);
            $debug[] = [$alias->name, $data['vnr'], $matchScore, $matchScore2, $similarityScore];

            if (false && $matchScore <= 100) {
                $makler = $alias?->gesellschafts_makler->makler;
                Vnralias::create(['name' => $data['vnr'], 'gm_id' => $alias->gm_id]);

                return false; // break loop
            }
        });
        dd($debug);

        if ($makler) {
            return new MaklerDTO($makler->name);
        }

        return null;
    }
}
