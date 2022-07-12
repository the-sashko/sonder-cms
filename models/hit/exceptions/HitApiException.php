<?php

namespace Sonder\Models\Hit\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Hit\Interfaces\IHitException;

#[ICoreException]
#[IHitException]
final class HitApiException extends HitException
{
    final public const MESSAGE_API_INPUT_VALUES_HAVE_INVALID_FORMAT = 'Input Values Are Not Set Or Have Invalid Format';

    final public const MESSAGE_API_ID_HAS_INVALID_FORMAT = 'ID Value Has Invalid Format';

    final public const MESSAGE_API_INVALID_TYPE = 'Invalid Type Of Hit';
}
