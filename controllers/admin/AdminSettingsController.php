<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminSettingsController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displaySettings(): ResponseObject
    {
        $this->assign([
            'page_path' => [
                '#' => 'Settings'
            ]
        ]);

        return $this->render('settings/list');
    }

    /**
     * @area admin
     * @route /admin/configs/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayConfigs(): ResponseObject
    {
        //TODO

        return $this->render('settings/config/list');
    }

    /**
     * @area admin
     * @route /admin/configs/view/([a-z-_]+)/
     * @url_params config_name=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayConfig(): ResponseObject
    {
        //TODO

        return $this->render('settings/config/list');
    }

    /**
     * @area admin
     * @route /admin/configs/edit/([a-z-_]+)/
     * @url_params config_name=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayEditConfig(): ResponseObject
    {
        //TODO

        return $this->render('settings/config/form');
    }
}