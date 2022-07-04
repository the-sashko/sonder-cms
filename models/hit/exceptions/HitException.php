<?php

namespace Sonder\Models\Hit\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException;
use Sonder\Models\Hit\Interfaces\IHitException;

#[ICoreException]
#[IHitException]
#[Attribute(Attribute::TARGET_CLASS)]
class HitException extends BaseException implements IHitException
{
    final public const CODE_SIMPLE_VALUES_OBJECT_INVALID_TYPE = 1001;

    final public const CODE_AGGREGATION_SIMPLE_VALUES_OBJECT_INVALID_TYPE = 2001;

    final public const CODE_API_INPUT_VALUES_HAVE_INVALID_FORMAT = 3001;
    final public const CODE_API_ID_HAS_INVALID_FORMAT = 3002;
    final public const CODE_API_INVALID_TYPE = 3003;
}
