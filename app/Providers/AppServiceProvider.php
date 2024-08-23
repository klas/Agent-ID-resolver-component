<?php

namespace App\Providers;

use App\Builder\DemvStepFilterBuilder;
use App\Builder\StepFilterBuilderInterface;
use App\Strategy\VnrResolvingStrategyInterface;
use App\Strategy\VnrStepFilteringResolvingStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        VnrResolvingStrategyInterface::class => VnrStepFilteringResolvingStrategy::class,
        StepFilterBuilderInterface::class => DemvStepFilterBuilder::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
