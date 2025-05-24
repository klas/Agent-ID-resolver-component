<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AID Resolution Strategy
    |--------------------------------------------------------------------------
    |
    | This option controls the default AID resolution strategy that will be used
    | by the application. The value should be the fully qualified class name
    | of one of the available strategies in the App\Strategy namespace.
    |
    | Available strategies:
    | - AidFuzzyResolvingStrategy: Uses fuzzy matching for AID resolution
    | - AidStepFilteringResolvingStrategy: Uses step filtering for AID resolution
    | - AidAlgoliaResolvingStrategy: Uses Algolia search for AID resolution
    |
    */
    'default_strategy' => env('AID_RESOLUTION_STRATEGY', \App\Strategy\AidFuzzyResolvingStrategy::class),
];
