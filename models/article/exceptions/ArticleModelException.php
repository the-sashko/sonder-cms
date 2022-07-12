<?php

namespace Sonder\Models\Article\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Article\Interfaces\IArticleException;

#[ICoreException]
#[IArticleException]
final class ArticleModelException extends ArticleException
{
    final public const MESSAGE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY = 'Can not find public directory path';

    final public const MESSAGE_MODEL_UPLOAD_FILE_ERROR = 'Can not upload file. Error: "%s"';
}
