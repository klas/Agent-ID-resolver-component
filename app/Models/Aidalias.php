<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Aidalias
 *
 * @property int $id
 * @property string $name
 * @property int $gm_id
 * @property CompaniesAgent $companies_agent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Aidalias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aidalias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aidalias query()
 * @method static \Illuminate\Database\Eloquent\Builder|Aidalias whereGmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aidalias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aidalias whereName($value)
 *
 * @mixin \Eloquent
 */
class Aidalias extends Model
{
    protected $table = 'aidaliases';

    public $timestamps = false;

    protected $casts = [
        'gm_id' => 'int',
    ];

    protected $fillable = [
        'name',
        'gm_id',
    ];

    public function companies_agent()
    {
        return $this->belongsTo(CompaniesAgent::class, 'gm_id');
    }
}
