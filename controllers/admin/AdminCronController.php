<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminCronController extends AdminBaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';

    /**
     * @area admin
     * @route /admin/cron/jobs((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCronJobs(): ResponseObject
    {
        //TODO
    }

    /**
     * @area admin
     * @route /admin/cron/jobs/view/([0-9]+)/
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
     * @route /admin/cron/job((/([0-9]+)/)|/)
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
     * @route /admin/cron/jobs/remove/([0-9]+)/
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
     * @route /admin/cron/jobs/restore/([0-9]+)/
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
