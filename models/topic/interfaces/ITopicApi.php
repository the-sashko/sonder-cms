<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicApi extends IModelApi
{
}
