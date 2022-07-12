<?php

namespace Sonder\Models\Topic\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\CMS\Essentials\BaseException;
use Sonder\Models\Topic\Interfaces\ITopicException;

#[ICoreException]
#[ITopicException]
class TopicException extends BaseException implements ITopicException
{
    final public const CODE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY = 1001;
    final public const CODE_MODEL_UPLOAD_FILE_ERROR = 1002;
}
