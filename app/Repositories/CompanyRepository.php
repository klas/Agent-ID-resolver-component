<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function findById(int $id): ?Company
    {
        return Company::find($id);
    }

    public function findByName(string $name): ?Company
    {
        return Company::where('name', $name)->first();
    }

    public function findByNameWithAgents(string $name): ?Company
    {
        return Company::where('name', $name)->with('agents')->first();
    }

    public function create(array $data): Company
    {
        $company = new Company;
        $company->fill($data);
        $company->save();

        return $company;
    }

    public function update(Company $company, array $data): Company
    {
        $company->fill($data);
        $company->save();

        return $company->refresh();
    }

    public function delete(Company $company): bool
    {
        return $company->delete();
    }

    public function getAll(): Collection
    {
        return Company::all();
    }

    public function attachAgent(Company $company, Agent $agent): void
    {
        $company->agents()->attach($agent);
    }

    public function detachAgent(Company $company, Agent $agent): void
    {
        $company->agents()->detach($agent);
    }
}
