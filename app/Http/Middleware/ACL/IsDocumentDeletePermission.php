<?php
declare(strict_types=1);

namespace App\Http\Middleware\ACL;

use Closure;

/**
 * Class IsDocumentDeletePermission
 * @package App\Http\Middleware\ACL
 */
class IsDocumentDeletePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
