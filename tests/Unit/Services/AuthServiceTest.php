<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Services\AuthService;
use App\Validators\UserValidator;

/**
 * Class AuthServiceTest
 * @package Tests\Unit\Services
 */
class AuthServiceTest extends TestCaseService
{
    /**
     * @var
     */
    protected $authService;

    /**
     * @var
     */
    protected $roleRepository;

    /**
     * @var
     */
    protected $userRepository;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->userRepository = $this->getMockRepository(UserRepository::class, [
            'find'
        ]);

        $this->roleRepository = $this->getMockRepository(RoleRepository::class);

        $this->authService = $this->getMockBuilder(AuthService::class)
            ->setConstructorArgs([
                $this->userRepository,
                app(UserValidator::class),
                $this->roleRepository
            ])
            ->setMethods(['validate'])
            ->getMock();
    }

    /**
     * @return bool
     */
    public function testCreateUser(): bool
    {
        $data = [
            'wefwefwe'
        ];

        $this->methodWillReturnTrue($this->authService, 'createUser');
        $this->assertTrue($this->authService->createUser($data));
    }
}
