<?php

namespace Sonder\Models\Shortener\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Shortener\Interfaces\IShortenerException;

#[ICoreException]
#[IShortenerException]
final class ShortenerApiException extends ShortenerException
{
    final public const MESSAGE_API_CAN_NOT_CREATE_SHORT_LINK = 'Can Not Create New Short Link';

    final public const MESSAGE_API_INPUT_VALUES_HAVE_INVALID_FORMAT = 'Input Values Are Not Set Or Have Invalid Format';

    final public const MESSAGE_API_ID_VALUE_HAS_INVALID_FORMAT ='ID Value Has Invalid Format';

    final public const MESSAGE_API_BOTH_ID_AND_CODE_CAN_NOT_BE_SET = 'Both Input Values "id" And "code" Can Not Be Set';

    final public const MESSAGE_API_SHORT_LINK_WITH_CODE_NOT_EXISTS ='Short Link With Code "%s" Not Exists';

    final public const MESSAGE_API_SHORT_LINK_WITH_ID_NOT_EXISTS = 'Short Link With ID "%d" Not Exists';
}
