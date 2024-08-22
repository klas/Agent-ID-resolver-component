<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Vnralias
 *
 * @property int $id
 * @property string $name
 * @property int $gm_id
 * @property GeselschaftsMakler $geselschafts_makler
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|Vnralias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vnralias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vnralias query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vnralias whereGmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vnralias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vnralias whereName($value)
 * @mixin \Eloquent
 */
class Vnralias extends Model
{
	protected $table = 'vnraliases';
	public $timestamps = false;

	protected $casts = [
		'gm_id' => 'int'
	];

	protected $fillable = [
		'name',
		'gm_id'
	];

	public function geselschafts_makler()
	{
		return $this->belongsTo(GeselschaftsMakler::class, 'gm_id');
	}
}
