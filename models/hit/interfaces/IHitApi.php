<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitApi extends IModelApi
{
}
