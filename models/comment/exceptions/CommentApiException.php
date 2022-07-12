<?php

namespace Sonder\Models\Comment\Exceptions;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Comment\Interfaces\ICommentException;

#[ICoreException]
#[ICommentException]
final class CommentApiException extends CommentException
{
    final public const MESSAGE_API_CAN_NOT_CREATE_COMMENT = 'Can Not Create New Comment';

    final public const MESSAGE_API_INPUT_VALUES_HAVE_INVALID_FORMAT = 'Input Values Are Not Set Or Have Invalid Format';

    final public const MESSAGE_API_ID_VALUE_HAS_INVALID_FORMAT = 'ID Value Has Invalid Format';

    final public const MESSAGE_API_BOTH_ID_AND_ARTICLE_ID_CAN_NOT_BE_SET = 'Both Input Values "id" And "article_id" Can Not Be Set';

    final public const MESSAGE_API_BOTH_ID_AND_USER_ID_CAN_NOT_BE_SET = 'Both Input Values "id" And "user_id" Can Not Be Set';

    final public const MESSAGE_API_BOTH_ARTICLE_ID_AND_USER_ID_CAN_NOT_BE_SET = 'Both Input Values "article_id" And "user_id" Can Not Be Set';

    final public const MESSAGE_API_COMMENT_WITH_ID_NOT_EXISTS = 'Comment With ID "%d" Not Exists';
}
