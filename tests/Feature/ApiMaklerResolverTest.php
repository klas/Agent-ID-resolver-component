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
    public function testShowReturnsValidData()
    {
        $vnr = '00654564';
        $geselchaft = 'Haftpflichtkasse Darmstadt';

        $response = $this->get("http://localhost/api/makler?vnr=$vnr&geselschaft=$geselchaft");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            [
                'name' => 'Max Mustermann'
            ]
        );
    }
}
