<?php

namespace Database\Seeders;

use App\Models\Gesellschaft;
use App\Models\Agent;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    use TestDataTrait;

    public function run(?int $dataColumn = null): void
    {
        $agentsData = self::AGENTS;
        $gesellschaftsAids = self::GESELSCHAFTS;

        $agents = [];

        foreach ($agentsData as $key => $agentName) {
            $agents[$key] = Agent::create(['name' => $agentName]);
        }

        foreach ($gesellschaftsAids as $key => $agentAids) {
            foreach ($agentAids as $gesellschaft => $aids) {
                $ges = Gesellschaft::firstOrCreate(
                    ['name' => $gesellschaft]
                );

                $ges->agents()->attach($agents[$key]);
                $ges->save();
                $ges->refresh();

                // Need to get it this way, pivot is empty in the original agent
                $agent = $ges->agents->firstWhere('name', '==', $agents[$key]->name);

                //Reduce data to specified column for variations
                if ($dataColumn) {
                    $aids = [$aids[$dataColumn]];
                }

                foreach ($aids as $aid) {
                    $agent->pivot->aidaliases()->create(['name' => $aid, 'gm_id' => $agent->pivot->id]);
                }
            }
        }
    }
}
