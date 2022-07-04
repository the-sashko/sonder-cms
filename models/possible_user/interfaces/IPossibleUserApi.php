<?php

namespace Sonder\Models\PossibleUser\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface IPossibleUserApi extends IModelApi
{
}
