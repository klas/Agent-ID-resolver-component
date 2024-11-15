<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 *
 * @property int $id
 * @property string $name
 * @property Collection|Agent[] $agents
 * @property-read \App\Models\CompaniesAgent $pivot
 * @property-read int|null $agents_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 *
 * @mixin \Eloquent
 */
class Company extends Model
{
    protected $table = 'companies';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'companies_agents')
            ->withPivot('id')
            ->using(CompaniesAgent::class);
    }
}
