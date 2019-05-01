<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RequestLog
 *
 * @property int $id
 * @property string $origin
 * @property int|null $app_id
 * @property int|null $user_id
 * @property string $method
 * @property string $uri
 * @property string $headers
 * @property string $params
 * @property string $ip
 * @property int|null $status_code
 * @property string|null $response
 * @property float|null $exec_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereExecTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RequestLog whereUserId($value)
 * @mixin \Eloquent
 */
class RequestLog extends Model
{
    protected $fillable = [
        'origin',
        'app_id',
        'user_id',
        'method',
        'uri',
        'headers',
        'params',
        'ip',
        'status_code',
        'response',
        'exec_time',
    ];
    //
}
