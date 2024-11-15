<?php

namespace App\Providers;

use App\Builder\IdMatcherStepFilterBuilder;
use App\Builder\StepFilterBuilderInterface;
use App\CoR\FilterDefinitions\AxaVersicherungFilterDefinition;
use App\CoR\FilterDefinitions\DieBayerischeFilterDefinition;
use App\CoR\FilterDefinitions\HaftpflichtkasseDarmstadtFilterDefinition;
use App\CoR\FilterDefinitions\IdealVersicherungFilterDefinition;
use App\CoR\FilterDefinitions\WWKFilterDefinition;
use App\Services\FuzzyInterface;
use App\Services\FuzzyService;
use App\Strategy\AidFuzzyResolvingStrategy;
use App\Strategy\AidResolvingStrategyInterface;
use App\Strategy\AidStepFilteringResolvingStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        //AidResolvingStrategyInterface::class => AidStepFilteringResolvingStrategy::class,
        AidResolvingStrategyInterface::class => AidFuzzyResolvingStrategy::class,
        StepFilterBuilderInterface::class => IdMatcherStepFilterBuilder::class,
        FuzzyInterface::class => FuzzyService::class,
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
