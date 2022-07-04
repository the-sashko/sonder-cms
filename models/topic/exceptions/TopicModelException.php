<?php

namespace Sonder\Models\Topic\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Topic\Interfaces\ITopicException;

#[ICoreException]
#[ITopicException]
#[Attribute(Attribute::TARGET_CLASS)]
final class TopicModelException extends TopicException
{
    final public const MESSAGE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY = 'Can not find public directory path';

    final public const MESSAGE_MODEL_UPLOAD_FILE_ERROR = 'Can not upload file. Error: "%s"';
}
