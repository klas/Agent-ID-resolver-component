<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Makler
 *
 * @property int $id
 * @property string $name
 * @property Collection|Gesellschaft[] $gesellschafts
 * @package App\Models
 * @property-read \App\Models\GesellschaftsMakler $pivot
 * @property-read int|null $gesellschafts_count
 * @method static \Illuminate\Database\Eloquent\Builder|Makler newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Makler newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Makler query()
 * @method static \Illuminate\Database\Eloquent\Builder|Makler whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Makler whereName($value)
 * @mixin \Eloquent
 */
class Makler extends Model
{
	protected $table = 'maklers';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function gesellschafts()
	{
		return $this->belongsToMany(Gesellschaft::class, 'gesellschafts_maklers')
            ->withPivot('id')
            ->using(GesellschaftsMakler::class);
	}
}
