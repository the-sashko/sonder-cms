<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelApi;

#[IModelApi]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleApi extends IModelApi
{
}
