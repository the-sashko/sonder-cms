<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleException extends ICoreException
{
}
