<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Gesellschaft
 *
 * @property int $id
 * @property string $name
 * @property Collection|Agent[] $agents
 * @property-read \App\Models\GesellschaftsAgent $pivot
 * @property-read int|null $agents_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Gesellschaft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gesellschaft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gesellschaft query()
 * @method static \Illuminate\Database\Eloquent\Builder|Gesellschaft whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gesellschaft whereName($value)
 *
 * @mixin \Eloquent
 */
class Gesellschaft extends Model
{
    protected $table = 'gesellschafts';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'gesellschafts_agents')
            ->withPivot('id')
            ->using(GesellschaftsAgent::class);
    }
}
