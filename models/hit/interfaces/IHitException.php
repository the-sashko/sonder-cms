<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitException extends ICoreException
{
}
