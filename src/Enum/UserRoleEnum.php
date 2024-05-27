<?php

namespace App\Enum;

use App\Service\EnumToArrayTrait;

enum UserRoleEnum:string
{
    use EnumToArrayTrait;

    case User = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';

}
