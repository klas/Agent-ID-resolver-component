<?php

namespace Database\Seeders;

use App\Models\Geselschaft;
use App\Models\Makler;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $max = Makler::create(['name' => 'Max Mustermann']);
        Makler::create(['name' => 'Nicht Max']);

        $geselschaftsVnrs = [
            'Haftpflichtkasse Darmstadt' => ['00654564', '654564', '654-564'],
            'WWK' => ['Q412548787', '412548787'],
            'Axa Versicherung' => ['15154184714-000', '15154184714', '99/15154184714'],
            'Ideal Versicherung' => ['006674BA23', '6674BA23', '6674-BA23'],
            'die Bayerische' => ['54501R784', '54501-R784', '54501784'],
        ];

        foreach ($geselschaftsVnrs AS $geselschaft => $vnrs) {
            $ges = Geselschaft::create(
                ['name' => $geselschaft]
            );

            $ges->maklers()->attach($max);
            $ges->save();
            $ges->refresh();

            foreach ($ges->maklers AS $makler) {
                foreach ($vnrs AS $vnr) {
                    $makler->pivot->vnraliases()->create(['name' => $vnr, 'gm_id' => $makler->pivot->id]);
                }
            }
        }


    }
}
