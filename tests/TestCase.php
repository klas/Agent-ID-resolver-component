<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations, WithFaker, InteractsWithDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:refresh --seed  --seeder=TestDataSeeder');
    }
}
