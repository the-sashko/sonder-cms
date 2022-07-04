<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicException extends ICoreException
{
}
