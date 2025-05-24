<?php

namespace App\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\AgentDTO;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AidStepFilteringResolvingStrategy implements AidResolvingStrategyInterface
{
    public function __construct(
        protected StepFilterBuilderInterface $stepFilterBuilder,
        protected AidAliasRepositoryInterface $aidAliasRepository
    ) {}

    public function resolve(array $data = []): ?AgentDTO
    {
        if (! isset($data['company']) || ! isset($data['aid'])) {
            throw new InvalidArgumentException;
        }

        // Try to match with stored aliases
        $agent = $this->aidAliasRepository->getAgentByExactAid($data['company'], $data['aid']);

        // No 100% match, try matching filtered value
        if (! $agent) {
            $filteredAid = $this->filterAid($data['aid'], $data['company']);

            // $filteredAid can be null, findByFilteredAid requires string
            $alias = $filteredAid ? $this->aidAliasRepository->findByFilteredAid($data['company'], $filteredAid) : null;

            if ($alias && ($agent = $alias->companies_agent->agent)) {
                $this->aidAliasRepository->create([
                    'name' => $data['aid'],
                    'gm_id' => $alias->gm_id,
                ]);
            }
        }

        // Still no match, try matching filtered stored aliases with filtered value
        if (! $agent) {
            $filteredAid = $filteredAid ?? $this->filterAid($data['aid'], $data['company']);
            $searchableAliases = $this->aidAliasRepository->getSearchableAidAliases($data['company']);

            foreach ($searchableAliases as $alias) {
                $aliasFilteredAid = $this->filterAid($alias->name, $data['company']);
                if ($filteredAid === $aliasFilteredAid) {
                    $agent = $alias->companies_agent->agent;

                    // Store both unfiltered and filtered aliases
                    $this->aidAliasRepository->create([
                        'name' => $data['aid'],
                        'gm_id' => $alias->gm_id,
                    ]);
                    $this->aidAliasRepository->create([
                        'name' => $filteredAid,
                        'gm_id' => $alias->gm_id,
                    ]);

                    break; // Exit loop
                }
            }
        }

        return $agent ? new AgentDTO($agent->name) : null;
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
