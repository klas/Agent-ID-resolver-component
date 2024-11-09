<?php

namespace Database\Seeders;

use App\Models\Gesellschaft;
use App\Models\Makler;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    use TestDataTrait;

    public function run(?int $dataColumn = null): void
    {
        $maklersData = self::MAKLERS;
        $gesellschaftsVnrs = self::GESELSCHAFTS;

        $maklers = [];

        foreach ($maklersData as $key => $maklerName) {
            $maklers[$key] = Makler::create(['name' => $maklerName]);
        }

        foreach ($gesellschaftsVnrs as $key => $maklerVnrs) {
            foreach ($maklerVnrs as $gesellschaft => $vnrs) {
                $ges = Gesellschaft::firstOrCreate(
                    ['name' => $gesellschaft]
                );

                $ges->maklers()->attach($maklers[$key]);
                $ges->save();
                $ges->refresh();

                // Need to get it this way, pivot is empty in the original makler
                $makler = $ges->maklers->firstWhere('name', '==', $maklers[$key]->name);

                //Reduce data to specified column for variations
                if ($dataColumn) {
                    $vnrs = [$vnrs[$dataColumn]];
                }

                foreach ($vnrs as $vnr) {
                    $makler->pivot->vnraliases()->create(['name' => $vnr, 'gm_id' => $makler->pivot->id]);
                }
            }
        }
    }
}
