<?php

namespace App\Enum;

use App\Service\EnumToArrayTrait;

enum CustomAttributeType : string
{
    use EnumToArrayTrait;
//    case Integer;
//    case String;
//    case Text;
//    case Boolean;
//    case Date;
    case Integer = 'Integer';
    case String = 'String';
    case Text = 'Text';
    case Boolean = 'Boolean';
    case Date = 'Date';
}
