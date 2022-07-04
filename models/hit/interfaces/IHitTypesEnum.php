<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreEnum;

#[ICoreEnum]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitTypesEnum extends ICoreEnum
{
}
