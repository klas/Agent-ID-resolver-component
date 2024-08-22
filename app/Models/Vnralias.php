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
 * @property int $geselschafts_maklers_id
 * 
 * @property GeselschaftsMakler $geselschafts_makler
 *
 * @package App\Models
 */
class Vnralias extends Model
{
	protected $table = 'vnraliases';
	public $timestamps = false;

	protected $casts = [
		'geselschafts_maklers_id' => 'int'
	];

	protected $fillable = [
		'name',
		'geselschafts_maklers_id'
	];

	public function geselschafts_makler()
	{
		return $this->belongsTo(GeselschaftsMakler::class, 'geselschafts_maklers_id');
	}
}
