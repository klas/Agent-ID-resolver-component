<?php
namespace App\Repositories;

use App\Models\Agent;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AgentRepository implements AgentRepositoryInterface
{
    public function findById(int $id): ?Agent
    {
        return Agent::find($id);
    }

    public function findByName(string $name): ?Agent
    {
        return Agent::where('name', $name)->first();
    }

    public function create(array $data): Agent
    {
        $agent = new Agent();
        $agent->fill($data);
        $agent->save();
        return $agent;
    }

    public function update(Agent $agent, array $data): Agent
    {
        $agent->fill($data);
        $agent->save();
        return $agent->refresh();
    }

    public function delete(Agent $agent): bool
    {
        return $agent->delete();
    }

    public function getAll(): Collection
    {
        return Agent::all();
    }
}
