<?php

namespace App\Providers;

use App\Builder\DemvStepFilterBuilder;
use App\Builder\StepFilterBuilderInterface;
use App\CoR\FilterDefinitions\AxaVersicherungFilterDefinition;
use App\CoR\FilterDefinitions\DieBayerischeFilterDefinition;
use App\CoR\FilterDefinitions\HaftpflichtkasseDarmstadtFilterDefinition;
use App\CoR\FilterDefinitions\IdealVersicherungFilterDefinition;
use App\CoR\FilterDefinitions\WWKFilterDefinition;
use App\Strategy\VnrFuzzyResolvingStrategy;
use App\Strategy\VnrResolvingStrategyInterface;
use App\Strategy\VnrStepFilteringResolvingStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        //VnrResolvingStrategyInterface::class => VnrStepFilteringResolvingStrategy::class,
        VnrResolvingStrategyInterface::class => VnrFuzzyResolvingStrategy::class,
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
                WWKFilterDefinition::class,
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
