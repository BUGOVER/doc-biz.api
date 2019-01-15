<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Repositories\Contracts\CompanyRepositoryInterface as CompanyRepository;
use Closure;
use LaraRepo\Criteria\Where\WhereCriteria;

/**
 * Class IsLeader
 * @package App\Http\Middleware
 */
class IsLeader
{
    /**
     * @var CompanyRepository
     */
    protected $companyRepository;

    /**
     * IsLeader constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
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
        $this->companyRepository->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $checkLeaderCompany = $this->companyRepository->findBy('leader_id', USER_ID);

        if (!$checkLeaderCompany) {
            return response(['message' => 'You Not leader, you PIRAT'], 500);
        }

        return $next($request);
    }
}
