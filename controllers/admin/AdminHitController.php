<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
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
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayHits(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hits/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayHit(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hit((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayHitForm(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hits/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveHit(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/articles/hits/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreHit(): ResponseObject
    {
        //TODO
    }
}
