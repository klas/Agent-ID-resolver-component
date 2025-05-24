<?php

namespace App\Providers;

use App\Builder\IdMatcherStepFilterBuilder;
use App\Builder\StepFilterBuilderInterface;
use App\CoR\FilterDefinitions\BimboInsuranceFilterDefinition;
use App\CoR\FilterDefinitions\DieHardFilterDefinition;
use App\CoR\FilterDefinitions\LiabilityInsuranceMagenstadtFilterDefinition;
use App\CoR\FilterDefinitions\MamaInsuranceFilterDefinition;
use App\CoR\FilterDefinitions\MMAFilterDefinition;
use App\Repositories\AgentRepository;
use App\Repositories\AidAliasRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Services\FuzzyInterface;
use App\Services\FuzzyService;
use App\Strategy\AidFuzzyResolvingStrategy;
use App\Strategy\AidResolvingStrategyInterface;
use App\Strategy\AidStepFilteringResolvingStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        // Strategy bindings
        AidResolvingStrategyInterface::class => AidFuzzyResolvingStrategy::class,
        // AidResolvingStrategyInterface::class => AidStepFilteringResolvingStrategy::class,

        // Service bindings
        StepFilterBuilderInterface::class => IdMatcherStepFilterBuilder::class,
        FuzzyInterface::class => FuzzyService::class,

        // Repository bindings
        AgentRepositoryInterface::class => AgentRepository::class,
        CompanyRepositoryInterface::class => CompanyRepository::class,
        AidAliasRepositoryInterface::class => AidAliasRepository::class,
    ];

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
            'filter_definition'
        );
    }

    public function boot(): void {}
}
