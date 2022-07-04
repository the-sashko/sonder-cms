<?php

namespace Sonder\Models\Topic\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException;
use Sonder\Models\Topic\Interfaces\ITopicException;

#[ICoreException]
#[ITopicException]
#[Attribute(Attribute::TARGET_CLASS)]
class TopicException extends BaseException implements ITopicException
{
    final public const CODE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY = 1001;
    final public const CODE_MODEL_UPLOAD_FILE_ERROR = 1002;
}
