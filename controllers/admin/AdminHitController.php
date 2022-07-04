<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\IResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminHitController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/articles/hits((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayHits(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hits/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayHit(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hit((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayHitForm(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hits/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveHit(): IResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hits/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreHit(): IResponseObject
    {
        //TODO
    }
}
