<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\MaklerDTO;
use App\Models\Geselschaft;
use App\Strategy\VnrResolvingStrategyInterface;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VnrStepFilteringResolvingStrategy implements VnrResolvingStrategyInterface
{

    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder) {}

    public function resolve(array $data = []): ?MaklerDTO
    {
        $geselschaft = Geselschaft::whereName($data['geselschaft'])->with('maklers')->firstOrFail();

        // Try to match with stored aliases
        $searchableAliases = collect([]);

        $geselschaft->maklers->each(function($makler) use(&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($makler->pivot->vnraliases);
        });

        $makler = $searchableAliases->firstWhere('name', '==', $data['vnr'])?->geselschafts_makler->makler;

        // No 100% match, try matching filtered value
        if (!$makler) {
            $filteredVnr = $this->filterVnr($data['vnr'], $data['geselschaft']);
            $makler = $searchableAliases->firstWhere('name', '==', $filteredVnr)?->geselschafts_makler->makler;
        }

        // Still no match, try matching filtered stored aliases with filtered value, if match store it for next time
        //$makler = $searchableAliases->firstWhere('name', '==', $filteredVnr)?->geselschafts_makler->makler;


        if ($makler) {
            return new MaklerDTO($makler->name);
        }

        return null;
    }

    protected function filterVnr(string $filterable, string $geselschaft): ?string
    {
        $filterDefinitions = App::tagged('filter_definition');

        $this->stepFilterBuilder->setFilterable($filterable);
        $handlerFound = false;

        foreach ($filterDefinitions AS $filterDefinition) {
            if ($handlerFound = $filterDefinition->responsible($geselschaft))
            {
                $filterDefinition->setStepFilterBuilder($this->stepFilterBuilder);
                $filterDefinition->runFilterChain();
                break;
            }
        }

        if (!$handlerFound) {
            throw new NotFoundHttpException('Filter Definition Not Found');
        }

        return $this->stepFilterBuilder->getFiltered();
    }
}
