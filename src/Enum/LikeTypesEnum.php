<?php

namespace App\Enum;

enum LikeTypesEnum : int
{
    case Like = 1;
    case Dislike = -1;
}
