<?php

namespace App\Repositories\Contracts;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Collection;

interface AgentRepositoryInterface
{
    public function findById(int $id): ?Agent;

    public function findByName(string $name): ?Agent;

    public function create(array $data): Agent;

    public function update(Agent $agent, array $data): Agent;

    public function delete(Agent $agent): bool;

    public function getAll(): Collection;
}
