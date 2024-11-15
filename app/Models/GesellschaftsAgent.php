<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class GesellschaftsAgent
 *
 * @property int $id
 * @property int $gesellschaft_id
 * @property int $agent_id
 * @property Gesellschaft $gesellschaft
 * @property Agent $agent
 * @property Collection|Aidalias[] $aidaliases
 * @property-read int|null $aidaliases_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsAgent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsAgent query()
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsAgent whereGesellschaftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsAgent whereAgentId($value)
 *
 * @mixin \Eloquent
 */
class GesellschaftsAgent extends Pivot
{
    protected $table = 'gesellschafts_agents';

    public $timestamps = false;

    public $incrementing = true;

    protected $casts = [
        'gesellschaft_id' => 'int',
        'agent_id' => 'int',
    ];

    protected $fillable = [
        'gesellschaft_id',
        'agent_id',
    ];

    public function gesellschaft()
    {
        return $this->belongsTo(Gesellschaft::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function aidaliases()
    {
        return $this->hasMany(Aidalias::class, 'gm_id');
    }
}
