<?php

namespace App\Strategy;

use App\Models\Gesellschaft;
use App\Models\GesellschaftsAgent;
use App\Models\Agent;
use App\Models\Aidalias;
use Illuminate\Support\Collection;
use function Psy\debug;

trait AidResolvingStrategyHelper
{
    protected function getSearchableAidAliases(string $gesellschaft): Collection
    {
        $gesellschaft = Gesellschaft::whereName($gesellschaft)->with('agents')->first();
        $searchableAliases = collect([]);

        $gesellschaft?->agents->each(function ($agent) use (&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($agent->pivot->aidaliases);
        });

        return $searchableAliases;
    }

    protected function getAgentPerExactAid(string $gesellschaft, string $aid): ?Agent
    {
        return Aidalias::where('name', '=', $aid)->with('gesellschafts_agent')->first()?->gesellschafts_agent->agent;
    }
}
