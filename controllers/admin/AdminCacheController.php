<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IResponseObject;

#[IController]
final class AdminCacheController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/cache/
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     */
    final public function displayCache(): IResponseObject
    {
        $this->assign([
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/settings/' => 'Settings',
                '/admin/settings/cache/' => 'Cache'
            ]
        ]);

        return $this->render('settings/cache');
    }

    /**
     * @area admin
     * @route /admin/settings/cache/remove/
     * @no_cache true
     * @return IResponseObject
     */
    final public function displayRemoveCache(): IResponseObject
    {
        //TODO

        return $this->redirect('/admin/settings/cache/');
    }
}
