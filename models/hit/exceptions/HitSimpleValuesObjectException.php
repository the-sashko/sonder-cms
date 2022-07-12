<?php

namespace Sonder\Models\Hit\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Hit\Interfaces\IHitException;

#[ICoreException]
#[IHitException]
final class HitSimpleValuesObjectException extends HitException
{
    final public const MESSAGE_SIMPLE_VALUES_OBJECT_INVALID_TYPE = 'Invalid Type Of Hit';
}
