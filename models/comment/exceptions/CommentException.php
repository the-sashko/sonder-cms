<?php

namespace Sonder\Models\Comment\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\CMS\Essentials\BaseException;
use Sonder\Models\Comment\Interfaces\ICommentException;

#[ICoreException]
#[ICommentException]
class CommentException extends BaseException implements ICommentException
{
    final public const CODE_API_CAN_NOT_CREATE_COMMENT = 1001;

    final public const CODE_API_INPUT_VALUES_HAVE_INVALID_FORMAT = 1002;

    final public const CODE_API_ID_VALUE_HAS_INVALID_FORMAT = 1003;

    final public const CODE_API_BOTH_ID_AND_ARTICLE_ID_CAN_NOT_BE_SET = 1004;

    final public const CODE_API_BOTH_ID_AND_USER_ID_CAN_NOT_BE_SET = 1005;

    final public const CODE_API_BOTH_ARTICLE_ID_AND_USER_ID_CAN_NOT_BE_SET = 1006;

    final public const CODE_API_COMMENT_WITH_ID_NOT_EXISTS = 1007;
}
