<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

class ApiMaklerResolverTest extends TestCase
{
    private const BASIC_URL = 'http://localhost/api/makler';

    public function testShowReturnsValidData()
    {
        $maklers['max'] = 'Max Mustermann';
        $maklers['not_max'] = 'Not Max';

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
                foreach ($vnrs as $vnr) {
                    $response = $this->get(self::BASIC_URL."?vnr=$vnr&gesellschaft=$gesellschaft");

                    $response->assertStatus(Response::HTTP_OK);
                    $response->assertJson(
                        [
                            'name' => $maklers[$key],
                        ]
                    );
                }
            }
        }
    }

    public function testShowReturnsErrorOnInvalidData()
    {
        $response = $this->get(self::BASIC_URL.'?vnr=00654564&gesellschaft=abc', ['Accept' => 'application/json']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(
            [
                'message' => 'No query results for model [App\\Models\\Gesellschaft].',
            ]
        );
    }
}
