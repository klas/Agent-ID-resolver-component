<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Geselschaft
 *
 * @property int $id
 * @property string $name
 * @property Collection|Makler[] $maklers
 * @package App\Models
 * @property-read \App\Models\GeselschaftsMakler $pivot
 * @property-read int|null $maklers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Geselschaft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Geselschaft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Geselschaft query()
 * @method static \Illuminate\Database\Eloquent\Builder|Geselschaft whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Geselschaft whereName($value)
 * @mixin \Eloquent
 */
class Geselschaft extends Model
{
	protected $table = 'geselschafts';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function maklers()
	{
		return $this->belongsToMany(Makler::class, 'geselschafts_maklers')
            ->withPivot('id')
            ->using(GeselschaftsMakler::class);
	}
}
