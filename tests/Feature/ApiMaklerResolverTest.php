<?php

namespace Tests\Feature;

use Database\Seeders\TestDataSeeder;
use Database\Seeders\TestDataTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiMaklerResolverTest extends TestCase
{
    private const BASIC_URL = 'http://localhost/api/makler';

    use TestDataTrait;

    public function testShowReturnsValidData()
    {
        $maklers = self::MAKLERS;
        $gesellschaftsVnrs = self::GESELSCHAFTS;

        for ($x = 0; $x < self::COLUMN_COUNT; $x++) {
            Artisan::call('migrate:fresh');
            App::make(TestDataSeeder::class)->run($x);

            foreach ($gesellschaftsVnrs as $key => $maklerVnrs) {
                foreach ($maklerVnrs as $gesellschaft => $vnrs) {
                    foreach ($vnrs as $vnr) {
                        $response = $this->getJson(self::BASIC_URL."?vnr=$vnr&gesellschaft=$gesellschaft");
                        //dump($x);
                        //$response->baseRequest->dump();
                        //$response->dump();
                        //dump($maklers[$key]);

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

    }

    public function testShowReturnsErrorOnInvalidData()
    {
        $response = $this->get(self::BASIC_URL.'?vnr=00123456&gesellschaft=abc', ['Accept' => 'application/json']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(
            [
                'message' => 'No query results for model [App\\Models\\Gesellschaft].',
            ]
        );
    }
}
