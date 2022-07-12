<?php

namespace Sonder\Models\Article\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\CMS\Essentials\BaseException;
use Sonder\Models\Article\Interfaces\IArticleException;

#[ICoreException]
#[IArticleException]
class ArticleException extends BaseException implements IArticleException
{
    final public const CODE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY = 1001;
    final public const CODE_MODEL_UPLOAD_FILE_ERROR = 1002;
}
