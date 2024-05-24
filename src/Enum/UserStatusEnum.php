<?php

namespace App\Enum;

enum UserStatusEnum : string
{
    case Blocked = 'blocked';
    case Active = 'active';
}
