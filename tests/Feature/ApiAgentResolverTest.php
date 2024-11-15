<?php

namespace Tests\Feature;

use Database\Seeders\TestDataSeeder;
use Database\Seeders\TestDataTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiAgentResolverTest extends TestCase
{
    private const BASIC_URL = 'http://localhost/api/agent';

    use TestDataTrait;

    public function testShowReturnsValidData()
    {
        $agents = self::AGENTS;
        $gesellschaftsAids = self::GESELSCHAFTS;

        for ($x = 0; $x < self::COLUMN_COUNT; $x++) {
            Artisan::call('migrate:fresh');
            App::make(TestDataSeeder::class)->run($x);

            foreach ($gesellschaftsAids as $key => $agentAids) {
                foreach ($agentAids as $gesellschaft => $aids) {
                    foreach ($aids as $aid) {
                        $response = $this->getJson(self::BASIC_URL."?aid=$aid&gesellschaft=$gesellschaft");
                        //dump($x);
                        //$response->baseRequest->dump();
                        //$response->dump();
                        //dump($agents[$key]);

                        $response->assertStatus(Response::HTTP_OK);
                        $response->assertJson(
                            [
                                'name' => $agents[$key],
                            ]
                        );

                    }
                }
            }
        }

    }

    public function testShowReturnsErrorOnInvalidData()
    {
        $response = $this->get(self::BASIC_URL.'?aid=00123456&gesellschaft=abc', ['Accept' => 'application/json']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(
            [
                'message' => 'Agent not found',
            ]
        );
    }
}
