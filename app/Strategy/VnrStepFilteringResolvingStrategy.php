<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\MaklerDTO;
use App\Strategy\VnrResolvingStrategyInterface;
use Illuminate\Support\Facades\App;

class VnrStepFilteringResolvingStrategy implements VnrResolvingStrategyInterface
{

    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder) {}

    public function resolve(array $data = []): ?MaklerDTO
    {
        $filteredVnr = $this->filterVnr($data['vnr'], $data['geselschaft']);

        return null;
    }

    protected function filterVnr(string $filterable, string $geselschaft)
    {
        $filterDefinitons = App::tagged('filter_definition');

        $filters = [];

        $this->stepFilterBuilder->setFilterable($filterable);

        foreach ($filterDefinitons AS $filterDefiniton) {
            if ($filterDefiniton->responsible($geselschaft))
            {
                $filterDefiniton->setStepFilterBuilder($this->stepFilterBuilder);
                $filterDefiniton->filterChain();
            }
        }



        foreach ($filters AS $filter) {

        }

        return $filtered;
    }
}
