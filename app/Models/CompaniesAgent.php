<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class CompaniesAgent
 *
 * @property int $id
 * @property int $company_id
 * @property int $agent_id
 * @property Company $company
 * @property Agent $agent
 * @property Collection|Aidalias[] $aidaliases
 * @property-read int|null $aidaliases_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompaniesAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompaniesAgent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompaniesAgent query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompaniesAgent whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompaniesAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompaniesAgent whereAgentId($value)
 *
 * @mixin \Eloquent
 */
class CompaniesAgent extends Pivot
{
    protected $table = 'companies_agents';

    public $timestamps = false;

    public $incrementing = true;

    protected $casts = [
        'company_id' => 'int',
        'agent_id' => 'int',
    ];

    protected $fillable = [
        'company_id',
        'agent_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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
