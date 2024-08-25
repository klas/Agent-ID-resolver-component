<?php

namespace App\Strategy;

use App\DTO\MaklerDTO;
use App\Models\Geselschaft;
use App\Models\Vnralias;
use App\Strategy\VnrResolvingStrategyInterface;

class VnrFuzzyResolvingStrategy implements VnrResolvingStrategyInterface
{

    public function resolve(array $data = []): ?MaklerDTO
    {
        $geselschaft = Geselschaft::whereName($data['geselschaft'])->with('maklers')->firstOrFail();
        $searchableAliases = collect([]);

        $geselschaft->maklers->each(function($makler) use(&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($makler->pivot->vnraliases);
        });

        $makler = $searchableAliases->whereStrict('name', $data['vnr'])->first()?->geselschafts_makler->makler;

        $searchableAliases->each(function($alias) use(&$makler, $data, $geselschaft) {
            if($filteredVnr === $this->filterVnr($alias->name, $data['geselschaft']))
            {
                $makler = $alias?->geselschafts_makler->makler;
                Vnralias::create(['name' => $data['vnr'], 'gm_id' => $alias->gm_id]);
                Vnralias::create(['name' => $filteredVnr, 'gm_id' => $alias->gm_id]);
                return false; // break loop
            };
        });


        if ($makler) {
            return new MaklerDTO($makler->name);
        }

        return null;
    }
}
