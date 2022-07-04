<?php

namespace Sonder\Models\Hit\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Hit\Interfaces\IHitException;

#[ICoreException]
#[IHitException]
#[Attribute(Attribute::TARGET_CLASS)]
final class HitSimpleValuesObjectException extends HitException
{
    final public const MESSAGE_SIMPLE_VALUES_OBJECT_INVALID_TYPE = 'Invalid Type Of Hit';
}
