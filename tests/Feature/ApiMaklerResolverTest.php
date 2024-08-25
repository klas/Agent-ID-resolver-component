<?php

namespace Tests\Feature;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Tests\TestCase;

class ApiMaklerResolverTest extends TestCase
{
    private const BASIC_URL = "http://localhost/api/makler";

    public function testShowReturnsValidData()
    {
        $maklers['max'] = 'Max Mustermann';
        $maklers['not_max'] = 'Not Max';

        $geselschaftsVnrs['max'] = [
            'Haftpflichtkasse Darmstadt' => ['00654564', '654564', '654-564'],
            'WWK' => ['Q412548787', '412548787'],
            'Axa Versicherung' => ['15154184714-000', '15154184714', '99/15154184714'],
            'Ideal Versicherung' => ['006674BA23', '6674BA23', '6674-BA23'],
            'die Bayerische' => ['54501R784', '54501-R784', '54501784'],
        ];

        $geselschaftsVnrs['not_max'] = [
            'Haftpflichtkasse Darmstadt' => ['00654574', '654574', '654-574'],
            'WWK' => ['Q412548777', '412548777'],
            'Axa Versicherung' => ['15154184774-000', '15154184774', '99/15154184774'],
            'Ideal Versicherung' => ['006674BA73', '6674BA73', '6674-BA73'],
            'die Bayerische' => ['54501R774', '54501-R774', '54501774'],
        ];

        foreach ($geselschaftsVnrs AS $key => $maklerVnrs) {
            foreach ($maklerVnrs AS $geselschaft => $vnrs) {
                foreach ($vnrs AS $vnr) {
                    $response = $this->get(self::BASIC_URL . "?vnr=$vnr&geselschaft=$geselschaft");

                    $response->assertStatus(Response::HTTP_OK);
                    $response->assertJson(
                        [
                            'name' => $maklers[$key]
                        ]
                    );
                }
            }
        }
    }

    public function testShowReturnsErrorOnInvalidData() {
        $response = $this->get(self::BASIC_URL . "?vnr=00654564&geselschaft=abc", ['Accept' =>'application/json']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(
            [
                'message' => 'No query results for model [App\\Models\\Geselschaft].'
            ]
        );
    }
}
