<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\Aidalias;
use App\Models\Company;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class AidAliasRepository implements AidAliasRepositoryInterface
{
    public function findById(int $id): ?Aidalias
    {
        return Aidalias::find($id);
    }

    public function findByName(string $name): ?Aidalias
    {
        return Aidalias::where('name', $name)->first();
    }

    public function findByNameWithAgent(string $name): ?Aidalias
    {
        return Aidalias::where('name', $name)
            ->with('companies_agent.agent')
            ->first();
    }

    public function findByCompany(string $companyName): EloquentCollection
    {
        return Aidalias::whereHas('companies_agent.company', function ($query) use ($companyName) {
            $query->where('name', $companyName);
        })->with('companies_agent.agent')->get();
    }

    public function create(array $data): Aidalias
    {
        $alias = new Aidalias;
        $alias->fill($data);
        $alias->save();

        return $alias;
    }

    public function update(Aidalias $alias, array $data): Aidalias
    {
        $alias->fill($data);
        $alias->save();

        return $alias->refresh();
    }

    public function delete(Aidalias $alias): bool
    {
        return $alias->delete();
    }

    public function getAll(): EloquentCollection
    {
        return Aidalias::all();
    }

    public function getAgentByExactAid(string $company, string $aid): ?Agent
    {
        $alias = Aidalias::where('name', $aid)
            ->with(['companies_agent.agent', 'companies_agent.company'])
            ->first();

        if (! $alias || ! $alias->companies_agent) {
            return null;
        }

        // Verify the agent belongs to the specified company
        if ($alias->companies_agent->company->name !== $company) {
            return null;
        }

        return $alias->companies_agent->agent;
    }

    public function getSearchableAidAliases(string $company): Collection
    {
        // This method returns a Support Collection to match original behavior
        $companyModel = Company::where('name', $company)->with('agents')->first();
        $searchableAliases = collect([]);

        $companyModel?->agents->each(function ($agent) use (&$searchableAliases) {
            $searchableAliases = $searchableAliases->merge($agent->pivot->aidaliases);
        });

        return $searchableAliases;
    }

    public function findByFilteredAid(string $company, string $filteredAid): ?Aidalias
    {
        return Aidalias::where('name', $filteredAid)
            ->whereHas('companies_agent.company', function ($query) use ($company) {
                $query->where('name', $company);
            })
            ->with(['companies_agent.agent', 'companies_agent.company'])
            ->first();
    }
}
