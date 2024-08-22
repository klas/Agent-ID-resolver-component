<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeselschaftsMakler
 * 
 * @property int $id
 * @property int $geselschaft_id
 * @property int $makler_id
 * 
 * @property Geselschaft $geselschaft
 * @property Makler $makler
 * @property Collection|Vnralias[] $vnraliases
 *
 * @package App\Models
 */
class GeselschaftsMakler extends Model
{
	protected $table = 'geselschafts_maklers';
	public $timestamps = false;

	protected $casts = [
		'geselschaft_id' => 'int',
		'makler_id' => 'int'
	];

	protected $fillable = [
		'geselschaft_id',
		'makler_id'
	];

	public function geselschaft()
	{
		return $this->belongsTo(Geselschaft::class);
	}

	public function makler()
	{
		return $this->belongsTo(Makler::class);
	}

	public function vnraliases()
	{
		return $this->hasMany(Vnralias::class, 'geselschafts_maklers_id');
	}
}
