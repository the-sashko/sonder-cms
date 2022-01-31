<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminCommentController extends AdminBaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';

    /**
     * @area admin
     * @route /admin/comments((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayComments(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/comments/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayComment(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/comment((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCommentForm(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/comments/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveComment(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/comments/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreComment(): ResponseObject
    {
        //TODO
    }
}
