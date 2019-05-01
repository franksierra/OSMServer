<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AppKey
 *
 * @property int $id
 * @property int $app_id
 * @property string $platform
 * @property string $key
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppKey whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AppKey extends Model
{

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'app_id');
    }

    /**
     * Get ApiKey record by key value
     *
     * @param string $key
     * @return bool
     */
    public static function getByKey($key)
    {
        return self::where([
            'key' => $key,
            'active' => 1
        ])->first();
    }
}
