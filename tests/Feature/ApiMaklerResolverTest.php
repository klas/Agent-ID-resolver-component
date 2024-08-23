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
    public function testResolverReturnsValidData()
    {
        $data = Activity::factory()->makeOne();
        $responseData = (new ActivityResource($data))->resolve();
        unset($responseData['id'], $responseData['created_at'], $responseData['updated_at']);


        $response = $this->call('GET', '/api/maklers', $data->toArray());
        //$response->dump();
        $response->assertStatus(201);
        $response->assertJson(
            [
                'inputData' => $responseData
            ]
        );
    }

}
