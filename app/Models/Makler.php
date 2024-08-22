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
 * @property Collection|Geselschaft[] $geselschafts
 * @package App\Models
 * @property-read \App\Models\GeselschaftsMakler $pivot
 * @property-read int|null $geselschafts_count
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

	public function geselschafts()
	{
		return $this->belongsToMany(Geselschaft::class, 'geselschafts_maklers')
            ->withPivot('id')
            ->using(GeselschaftsMakler::class);
	}
}
