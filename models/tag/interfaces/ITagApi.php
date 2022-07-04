<?php

namespace Sonder\Models\Tag\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITagApi extends IModelApi
{
}
