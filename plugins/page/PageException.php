<?php

namespace Sonder\Plugins\Page\Exceptions;

use Exception;
use Throwable;

final class PageException extends Exception implements Throwable
{
    final public const CODE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET = 1000;
    final public const CODE_PLUGIN_TEMPLATE_NOT_EXISTS = 1001;
    final public const CODE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT = 1002;

    final public const MESSAGE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET = 'Static Page Name Is Not Set';

    final public const MESSAGE_PLUGIN_TEMPLATE_NOT_EXISTS = 'Template For Static Page Not Exists';

    final public const MESSAGE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT = 'Static Page File Has Bad Format';
}
