<?php

namespace Database\Seeders;

use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    use TestDataTrait;

    public function __construct(
        protected AgentRepositoryInterface $agentRepository,
        protected CompanyRepositoryInterface $companyRepository,
        protected AidAliasRepositoryInterface $aidAliasRepository
    ) {}

    public function run(?int $dataColumn = null): void
    {
        $agentsData = self::AGENTS;
        $companiesAids = self::COMPANIES;

        $agents = [];

        foreach ($agentsData as $key => $agentName) {
            $agents[$key] = $this->agentRepository->create(['name' => $agentName]);
        }

        foreach ($companiesAids as $key => $agentAids) {
            foreach ($agentAids as $companyName => $aids) {
                $company = $this->companyRepository->findByName($companyName)
                    ?? $this->companyRepository->create(['name' => $companyName]);

                $this->companyRepository->attachAgent($company, $agents[$key]);

                // Refresh to get pivot relationship
                $company = $this->companyRepository->findByNameWithAgents($companyName);
                $agent = $company->agents->firstWhere('name', '==', $agents[$key]->name);

                // Reduce data to specified column for variations
                if ($dataColumn) {
                    $aids = [$aids[$dataColumn]];
                }

                foreach ($aids as $aid) {
                    $this->aidAliasRepository->create([
                        'name' => $aid,
                        'gm_id' => $agent->pivot->id,
                    ]);
                }
            }
        }
    }
}
