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
 * 
 * @property Collection|Makler[] $maklers
 *
 * @package App\Models
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
					->withPivot('id');
	}
}
