<?php

namespace App\Providers;

use App\Builder\IdMatcherStepFilterBuilder;
use App\Builder\StepFilterBuilderInterface;
use App\CoR\FilterDefinitions\MamaInsuranceFilterDefinition;
use App\CoR\FilterDefinitions\DieHardFilterDefinition;
use App\CoR\FilterDefinitions\LiabilityInsuranceMagenstadtFilterDefinition;
use App\CoR\FilterDefinitions\BimboInsuranceFilterDefinition;
use App\CoR\FilterDefinitions\MMAFilterDefinition;
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
                MamaInsuranceFilterDefinition::class,
                DieHardFilterDefinition::class,
                LiabilityInsuranceMagenstadtFilterDefinition::class,
                BimboInsuranceFilterDefinition::class,
                MMAFilterDefinition::class,
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
