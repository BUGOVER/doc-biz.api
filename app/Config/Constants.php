<?php
declare(strict_types=1);

namespace App\Config;

/**
 * Class PersonStatus
 * @package App\Config
 */
class ConstPersonRole
{
    public const LEADER = 1;
    public const ADMIN = 2;
    public const USER = 3;
}

/**
 * Class ConsPermissionType
 * @package App\Config
 */
class ConstPersonPermission
{
    public const READ = 1;
    public const WRITE = 2;
    public const EXECUTE = 3;

    public const WITHOUT_PERMISSION = 0;
}

/**
 * Class ConstGroupType
 * @package App\Config
 */
class ConstGroupType
{
    public const PRIVATE = 1;
    public const PUBLIC = 2;
}

/**
 * Class ConstEmailTemplateType
 * @package App\Config
 */
class ConstEmailTemplateType
{
    public const WELCOME_EMAIL = 1;
    public const CONFIRM_COMPANY_KEY_EMAIL = 2;
    public const USER_INVITATION_EMAIL = 3;
    public const USER_RESET_PASSWORD_EMAIL = 4;
    public const USER_DELETED_IN_COMPANY = 5;
}
