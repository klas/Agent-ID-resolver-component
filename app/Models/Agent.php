<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Agent
 *
 * @property int $id
 * @property string $name
 * @property Collection|Company[] $companies
 * @property-read \App\Models\CompaniesAgent $pivot
 * @property-read int|null $companies_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereName($value)
 *
 * @mixin \Eloquent
 */
class Agent extends Model
{
    protected $table = 'agents';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'companies_agents')
            ->withPivot('id')
            ->using(CompaniesAgent::class);
    }
}
