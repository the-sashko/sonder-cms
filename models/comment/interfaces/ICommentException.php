<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentException extends ICoreException
{
}
