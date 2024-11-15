<?php

namespace App\Strategy;

use App\Models\Gesellschaft;
use App\Models\GesellschaftsMakler;
use App\Models\Makler;
use App\Models\Vnralias;
use Illuminate\Support\Collection;
use function Psy\debug;

trait VnrResolvingStrategyHelper
{
    protected function getSearchableVnrAliases(string $gesellschaft): Collection
    {
        $gesellschaft = Gesellschaft::whereName($gesellschaft)->with('maklers')->first();
        $searchableAliases = collect([]);

        $gesellschaft?->maklers->each(function ($makler) use (&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($makler->pivot->vnraliases);
        });

        return $searchableAliases;
    }

    protected function getMaklerPerExactVnr(string $gesellschaft, string $vnr): ?Makler
    {
        return Vnralias::where('name', '=', $vnr)->with('gesellschafts_makler')->first()?->gesellschafts_makler->makler;
    }
}
