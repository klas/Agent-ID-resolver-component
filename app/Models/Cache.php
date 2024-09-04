<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cache
 *
 * @property string $key
 * @property string $value
 * @property int $expiration
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Cache newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cache newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cache query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereValue($value)
 *
 * @mixin \Eloquent
 */
class Cache extends Model
{
    protected $table = 'cache';

    protected $primaryKey = 'key';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'expiration' => 'int',
    ];

    protected $fillable = [
        'value',
        'expiration',
    ];
}
