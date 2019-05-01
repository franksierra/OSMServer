<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\App
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\App whereUserId($value)
 * @mixin \Eloquent
 */
class App extends Model
{
    //
}
