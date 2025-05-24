<?php

namespace App\Repositories\Contracts;

use App\Models\Agent;
use App\Models\Aidalias;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface AidAliasRepositoryInterface
{
    public function findById(int $id): ?Aidalias;

    public function findByName(string $name): ?Aidalias;

    public function findByNameWithAgent(string $name): ?Aidalias;

    public function findByCompany(string $companyName): EloquentCollection;

    public function create(array $data): Aidalias;

    public function update(Aidalias $alias, array $data): Aidalias;

    public function delete(Aidalias $alias): bool;

    public function getAll(): EloquentCollection;

    public function getAgentByExactAid(string $company, string $aid): ?Agent;

    public function getSearchableAidAliases(string $company): Collection;

    public function findByFilteredAid(string $company, string $filteredAid): ?Aidalias;
}
