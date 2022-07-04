<?php

namespace Sonder\Models\Hit\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Models\Hit\Interfaces\IHitTypesEnum;

#[ICoreEnum]
#[IHitTypesEnum]
enum HitTypesEnum: string implements IHitTypesEnum
{
    case ARTICLE = 'article';
    case TAG = 'tag';
    case TOPIC = 'topic';
}
