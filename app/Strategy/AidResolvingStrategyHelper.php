<?php

namespace App\Strategy;

use App\Models\Company;
use App\Models\CompaniesAgent;
use App\Models\Agent;
use App\Models\Aidalias;
use Illuminate\Support\Collection;
use function Psy\debug;

trait AidResolvingStrategyHelper
{
    protected function getSearchableAidAliases(string $company): Collection
    {
        $company = Company::whereName($company)->with('agents')->first();
        $searchableAliases = collect([]);

        $company?->agents->each(function ($agent) use (&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($agent->pivot->aidaliases);
        });

        return $searchableAliases;
    }

    protected function getAgentPerExactAid(string $company, string $aid): ?Agent
    {
        return Aidalias::where('name', '=', $aid)->with('companies_agent')->first()?->companies_agent->agent;
    }
}
