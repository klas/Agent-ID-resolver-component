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
        $filteredVnr = $this->filterVnr($data['vnr'], $data['geselschaft']);
        $geselschaft = Geselschaft::whereName($data['geselschaft'])->with('maklers')->first();

        // Try to match with stored aliases
        $maklers = $geselschaft->maklers;

        $searchableAliases = collect([]);

        $geselschaft->maklers->each(function($makler) use(&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($makler->pivot->vnraliases);
        });

        $makler = $searchableAliases->firstWhere('name', '==', $filteredVnr)?->geselschafts_makler->makler;



        // No 100% match, try matching filtered stored aliases

        // Still no match, try searching


        if ($makler) {
            return new MaklerDTO($makler->name);
        }

        return null;
    }

    protected function filterVnr(string $filterable, string $geselschaft): ?string
    {
        $filterDefinitons = App::tagged('filter_definition');

        $this->stepFilterBuilder->setFilterable($filterable);
        $handlerFound = false;

        foreach ($filterDefinitons AS $filterDefiniton) {
            if ($handlerFound = $filterDefiniton->responsible($geselschaft))
            {
                $filterDefiniton->setStepFilterBuilder($this->stepFilterBuilder);
                $filterDefiniton->runFilterChain();
                break;
            }
        }

        if (!$handlerFound) {
            throw new NotFoundHttpException('Filter Definition Not Found');
        }

        return $this->stepFilterBuilder->getFiltered();
    }
}
