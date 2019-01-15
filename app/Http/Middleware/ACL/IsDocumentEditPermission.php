<?php
declare(strict_types=1);

namespace App\Http\Middleware\ACL;

use App\Services\DocumentService;
use Closure;

/**
 * Class IsDocumentEditPermission
 * @package App\Http\Middleware
 */
class IsDocumentEditPermission
{
    /**
     * @var DocumentService
     */
    protected $documentService;

    /**
     * IsDocumentEditPermission constructor.
     * @param DocumentService $documentRepository
     */
    public function __construct(DocumentService $documentRepository)
    {
        $this->documentService = $documentRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return response($request->all());
//        $check = $this->documentService->checkPermissionEdit($data);

//        return $next(api$request);
    }
}
