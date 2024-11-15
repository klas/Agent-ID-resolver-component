<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\AgentDTO;
use App\Models\Aidalias;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AidStepFilteringResolvingStrategy implements AidResolvingStrategyInterface
{
    use AidResolvingStrategyHelper;

    public function __construct(protected StepFilterBuilderInterface $stepFilterBuilder) {}

    public function resolve(array $data = []): ?AgentDTO
    {
        if (! isset($data['company']) || ! isset($data['aid'])) {
            throw new InvalidArgumentException;
        }

        // Try to match with stored aliases
        $agent = $this->getAgentPerExactAid($data['company'], $data['aid']);

        // No 100% match, try matching filtered value
        if (! $agent) {
            $searchableAliases = $this->getSearchableAidAliases($data['company']);
            $filteredAid = $this->filterAid($data['aid'], $data['company']);
            $alias = $searchableAliases->whereStrict('name', $filteredAid)->first();
            if ($agent = $alias?->companies_agent->agent) {
                Aidalias::create(['name' => $data['aid'], 'gm_id' => $alias->gm_id]);
            }
        }

        // Still no match, try matching filtered stored aliases with filtered value,
        // if match store unfiltered and filtered value for next time for performance
        if (! $agent) {
            $searchableAliases->each(function ($alias) use (&$agent, $filteredAid, $data) {
                if ($filteredAid === $this->filterAid($alias->name, $data['company'])) {
                    $agent = $alias?->companies_agent->agent;
                    Aidalias::create(['name' => $data['aid'], 'gm_id' => $alias->gm_id]);
                    Aidalias::create(['name' => $filteredAid, 'gm_id' => $alias->gm_id]);

                    return false; // break loop
                }
            });
        }

        if ($agent) {
            return new AgentDTO($agent->name);
        }

        return null;
    }

    protected function filterAid(string $filterable, string $company): ?string
    {
        $filterDefinitions = App::tagged('filter_definition');

        $this->stepFilterBuilder->setFilterable($filterable);
        $handlerFound = false;

        foreach ($filterDefinitions as $filterDefinition) {
            if ($handlerFound = $filterDefinition->responsible($company)) {
                $filterDefinition->setStepFilterBuilder($this->stepFilterBuilder);
                $filterDefinition->runFilterChain();
                break;
            }
        }

        if (! $handlerFound) {
            throw new NotFoundHttpException('Filter Definition Not Found');
        }

        return $this->stepFilterBuilder->getFiltered();
    }
}
