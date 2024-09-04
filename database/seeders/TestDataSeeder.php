<?php

namespace Database\Seeders;

use App\Models\Gesellschaft;
use App\Models\Makler;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $maklers['max'] = Makler::create(['name' => 'Max Mustermann']);
        $maklers['not_max'] = Makler::create(['name' => 'Not Max']);

        $gesellschaftsVnrs['max'] = [
            'Haftpflichtkasse Darmstadt' => ['00654564', '654564', '654-564'],
            'WWK' => ['Q412548787', '412548787'],
            'Axa Versicherung' => ['15154184714-000', '15154184714', '99/15154184714'],
            'Ideal Versicherung' => ['006674BA23', '6674BA23', '6674-BA23'],
            'die Bayerische' => ['54501R784', '54501-R784', '54501784'],
        ];

        $gesellschaftsVnrs['not_max'] = [
            'Haftpflichtkasse Darmstadt' => ['00654574', '654574', '654-574'],
            'WWK' => ['Q412548777', '412548777'],
            'Axa Versicherung' => ['15154184774-000', '15154184774', '99/15154184774'],
            'Ideal Versicherung' => ['006674BA73', '6674BA73', '6674-BA73'],
            'die Bayerische' => ['54501R774', '54501-R774', '54501774'],
        ];

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

                foreach ($vnrs as $vnr) {
                    $makler->pivot->vnraliases()->create(['name' => $vnr, 'gm_id' => $makler->pivot->id]);
                }
            }
        }
    }
}
