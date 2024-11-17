<?php

namespace Database\Seeders;

trait TestDataTrait
{
    public const AGENTS = [
        'max' => 'Max Mustermann',
        'not_max' => 'Not Max',
    ];

    public const COMPANIES = [
        'max' => [
            'Liability Insurance Magenstadt' => ['00654564', '654564', '654-564'],
            'MMA' => ['Q412548787', '412548787', 'QQ412548787'],
            'Mama Insurance' => ['15154184714-000', '15154184714', '99/15154184714'],
            'Bimbo Insurance' => ['006674BA23', '6674BA23', '6674-BA23'],
            'Die Hard' => ['54501R784', '54501-R784', '54501784'],
        ],

        'not_max' => [
            'Liability Insurance Magenstadt' => ['00654574', '654574', '654-574'],
            'MMA' => ['Q412548777', '412548777', 'QQ412548777'],
            'Mama Insurance' => ['15154184774-000', '15154184774', '99/15154184774'],
            'Bimbo Insurance' => ['006674BA73', '6674BA73', '6674-BA73'],
            'Die Hard' => ['54501R774', '54501-R774', '54501774'],
        ],
    ];

    public const COLUMN_COUNT = 3;
}
