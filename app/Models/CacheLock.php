<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CacheLock
 *
 * @property string $key
 * @property string $owner
 * @property int $expiration
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CacheLock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CacheLock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CacheLock query()
 * @method static \Illuminate\Database\Eloquent\Builder|CacheLock whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CacheLock whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CacheLock whereOwner($value)
 *
 * @mixin \Eloquent
 */
class CacheLock extends Model
{
    protected $table = 'cache_locks';

    protected $primaryKey = 'key';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'expiration' => 'int',
    ];

    protected $fillable = [
        'owner',
        'expiration',
    ];
}
