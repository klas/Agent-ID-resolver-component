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
 * 
 * @property Collection|Geselschaft[] $geselschafts
 *
 * @package App\Models
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
					->withPivot('id');
	}
}
