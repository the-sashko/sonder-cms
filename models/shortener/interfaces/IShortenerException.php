<?php

namespace Sonder\Models\Shortener\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface IShortenerException extends ICoreException
{
}
