<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class GesellschaftsMakler
 *
 * @property int $id
 * @property int $gesellschaft_id
 * @property int $makler_id
 * @property Gesellschaft $gesellschaft
 * @property Makler $makler
 * @property Collection|Vnralias[] $vnraliases
 * @package App\Models
 * @property-read int|null $vnraliases_count
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsMakler newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsMakler newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsMakler query()
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsMakler whereGesellschaftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsMakler whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GesellschaftsMakler whereMaklerId($value)
 * @mixin \Eloquent
 */
class GesellschaftsMakler extends Pivot
{
	protected $table = 'gesellschafts_maklers';
	public $timestamps = false;
    public $incrementing = true;

	protected $casts = [
		'gesellschaft_id' => 'int',
		'makler_id' => 'int'
	];

	protected $fillable = [
		'gesellschaft_id',
		'makler_id'
	];

	public function gesellschaft()
	{
		return $this->belongsTo(Gesellschaft::class);
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
