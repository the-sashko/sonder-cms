<?php

namespace Sonder\Models\Shortener\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException;
use Sonder\Models\Shortener\Interfaces\IShortenerException;

#[ICoreException]
#[IShortenerException]
#[Attribute(Attribute::TARGET_CLASS)]
class ShortenerException extends BaseException implements IShortenerException
{
    final public const CODE_API_CAN_NOT_CREATE_SHORT_LINK = 1001;

    final public const CODE_API_INPUT_VALUES_HAVE_INVALID_FORMAT = 1002;

    final public const CODE_API_ID_VALUE_HAS_INVALID_FORMAT = 1003;

    final public const CODE_API_BOTH_ID_AND_CODE_CAN_NOT_BE_SET = 1004;

    final public const CODE_API_SHORT_LINK_WITH_CODE_NOT_EXISTS = 1005;

    final public const CODE_API_SHORT_LINK_WITH_ID_NOT_EXISTS = 1006;
}
