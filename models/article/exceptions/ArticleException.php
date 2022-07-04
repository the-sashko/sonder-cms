<?php

namespace Sonder\Models\Article\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException;
use Sonder\Models\Article\Interfaces\IArticleException;

#[ICoreException]
#[IArticleException]
#[Attribute(Attribute::TARGET_CLASS)]
class ArticleException extends BaseException implements IArticleException
{
    final public const CODE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY = 1001;
    final public const CODE_MODEL_UPLOAD_FILE_ERROR = 1002;
}
