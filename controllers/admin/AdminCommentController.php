<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\ResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminCommentController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/articles/comments((/page-([0-9]+)/)|/)
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
     * @route /admin/articles/comments/view/([0-9]+)/
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
     * @route /admin/articles/comment((/([0-9]+)/)|/)
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
     * @route /admin/articles/comments/remove/([0-9]+)/
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
     * @route /admin/articles/comments/restore/([0-9]+)/
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
