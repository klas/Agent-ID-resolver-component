<?php

namespace App\Repositories\Contracts;

use App\Models\Agent;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    public function findById(int $id): ?Company;

    public function findByName(string $name): ?Company;

    public function findByNameWithAgents(string $name): ?Company;

    public function create(array $data): Company;

    public function update(Company $company, array $data): Company;

    public function delete(Company $company): bool;

    public function getAll(): Collection;

    public function attachAgent(Company $company, Agent $agent): void;

    public function detachAgent(Company $company, Agent $agent): void;
}
