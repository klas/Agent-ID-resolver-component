<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class GeselschaftsMakler
 *
 * @property int $id
 * @property int $geselschaft_id
 * @property int $makler_id
 * @property Geselschaft $geselschaft
 * @property Makler $makler
 * @property Collection|Vnralias[] $vnraliases
 * @package App\Models
 * @property-read int|null $vnraliases_count
 * @method static \Illuminate\Database\Eloquent\Builder|GeselschaftsMakler newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeselschaftsMakler newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeselschaftsMakler query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeselschaftsMakler whereGeselschaftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeselschaftsMakler whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeselschaftsMakler whereMaklerId($value)
 * @mixin \Eloquent
 */
class GeselschaftsMakler extends Pivot
{
	protected $table = 'geselschafts_maklers';
	public $timestamps = false;
    public $incrementing = true;

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
		return $this->hasMany(Vnralias::class, 'gm_id');
	}
}
