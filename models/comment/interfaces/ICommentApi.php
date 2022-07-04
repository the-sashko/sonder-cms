<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentApi extends IModelApi
{
}
