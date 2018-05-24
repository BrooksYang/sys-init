<?php

namespace App\Http\Middleware;

use App\Traits\MongoLogHelper;
use Closure;


/**
 * Class WriteLog
 * @package App\Http\Middleware
 * 管理端日志
 */
class WriteLog
{
    use MongoLogHelper;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $data = ['level' => $this->getLevel($request->method())];
        $this->LogInto($data);

        return $response;
    }

    /**
     * Get log level
     *
     * @param $method
     * @return mixed
     */
    public function getLevel($method)
    {
         $level = [
            'GET' => 'Info',
            'DELETE' => 'Warning',
            'POST' => 'Notice',
            'PATCH' => 'Notice',
         ];

         return $level[$method];
    }
}
