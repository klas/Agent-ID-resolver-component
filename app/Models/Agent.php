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
 * @property Collection|Gesellschaft[] $gesellschafts
 * @property-read \App\Models\GesellschaftsAgent $pivot
 * @property-read int|null $gesellschafts_count
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

    public function gesellschafts()
    {
        return $this->belongsToMany(Gesellschaft::class, 'gesellschafts_agents')
            ->withPivot('id')
            ->using(GesellschaftsAgent::class);
    }
}
