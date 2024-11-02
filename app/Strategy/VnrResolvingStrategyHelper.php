<?php

namespace App\Strategy;

use App\Models\Gesellschaft;
use Illuminate\Support\Collection;

trait VnrResolvingStrategyHelper
{

    protected function getSearchableAliases(string $gesellschaft): Collection
    {
        $gesellschaft = Gesellschaft::whereName($gesellschaft)->with('maklers')->firstOrFail();
        $searchableAliases = collect([]);

        $gesellschaft->maklers->each(function ($makler) use (&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($makler->pivot->vnraliases);
        });

        return $searchableAliases;
    }

    protected function getDatabaseMatch()
    {

    }
}
