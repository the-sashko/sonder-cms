<?php

namespace Sonder\Plugins\Page\Exceptions;

use Exception;

final class PageException extends Exception
{
    const CODE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET = 1000;
    const CODE_PLUGIN_TEMPLATE_NOT_EXISTS = 1001;
    const CODE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT = 1002;

    const MESSAGE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET = 'Static Page Name Is ' .
    'Not Set';

    const MESSAGE_PLUGIN_TEMPLATE_NOT_EXISTS = 'Template For Static Page Not ' .
    'Exists';

    const MESSAGE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT = 'Static Page File ' .
    'Has Bad Format';
}
