<?php

namespace Sonder\Models\Shortener\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface IShortenerApi extends IModelApi
{
}
