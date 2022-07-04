<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\IResponseObject;
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
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayComments(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/comments/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayComment(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/comment((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCommentForm(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/comments/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveComment(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/comments/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreComment(): IResponseObject
    {
        //TODO
    }
}
