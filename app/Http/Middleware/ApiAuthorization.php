<?php

namespace App\Http\Middleware;

use App\Models\AppKey;
use App\Models\RequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Closure;

class ApiAuthorization
{

    const AUTH_HEADER = 'X-Authorization';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = $request->header(self::AUTH_HEADER);
        $apiKey = AppKey::getByKey($header);

        // Aqui voy a guardar las peticiones
        if ($apiKey instanceof AppKey) {
            $request_log = $this->logAccessEvent($request, $apiKey);
            $request->request->add(['request_log' => $request_log]);
            return $next($request);
        }

        $this->logAccessEvent($request);
        return response([
            'errors' => [[
                'message' => 'Unauthorized'
            ]]
        ], 401);

    }

    public function terminate($request, $response)
    {
        $this->updateAccessEvent($request, $response);
    }


    protected function logAccessEvent(Request $request, AppKey $apiKey = null)
    {
        $request_log = new RequestLog([
            'origin' => 'api',
            'app_id' => $apiKey->app_id ?? null,
//            'user_id' => $apiKey->app_id,
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
            'headers' => json_encode($request->headers->all()),
            'params' => json_encode($request->request->all()),
            'ip' => $request->getClientIp(),
        ]);
        $request_log->save();
        return $request_log;

    }

    protected function updateAccessEvent(Request $request, JsonResponse $response)
    {
        $request_log = RequestLog::find($request->request_log->id);
        $request_log->status_code = $response->getStatusCode();
        $request_log->response = json_encode($response->getOriginalContent());
        $request_log->exec_time = microtime(true) - LARAVEL_START;
        $request_log->save();

    }
}
