<?php

namespace App\Providers;

use App\Builder\DemvStepFilterBuilder;
use App\Builder\StepFilterBuilderInterface;
use App\FilterDefinitions\AxaVersicherungFilterDefinition;
use App\FilterDefinitions\DieBayerischeFilterDefinition;
use App\FilterDefinitions\HaftpflichtkasseDarmstadtFilterDefinition;
use App\FilterDefinitions\IdealVersicherungFilterDefinition;
use App\FilterDefinitions\WWKFilterDefinition;
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
        $this->app->tag(
            [
                AxaVersicherungFilterDefinition::class,
                DieBayerischeFilterDefinition::class,
                HaftpflichtkasseDarmstadtFilterDefinition::class,
                IdealVersicherungFilterDefinition::class,
                WWKFilterDefinition::class
            ],
            'filter_definition');

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
