<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminCronController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/cron((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCron(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/settings/cron/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCronJob(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/settings/cron((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCronJobForm(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/settings/cron/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveCronJob(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/settings/cron/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreCronJob(): ResponseObject
    {
        //TODO
    }
}
