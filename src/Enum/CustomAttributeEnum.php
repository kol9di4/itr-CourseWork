<?php

namespace App\Enum;

use App\Service\EnumToArrayTrait;

enum CustomAttributeEnum : string
{
    case Integer = 'Integer';
    case String = 'String';
    case Text = 'Text';
    case Boolean = 'Boolean';
    case Date = 'Date';
}
