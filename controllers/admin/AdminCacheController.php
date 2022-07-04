<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\IResponseObject;

final class AdminCacheController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/cache/
     * @no_cache true
     *
     * @return IResponseObject
     * @throws Exception
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
     */
    final public function displayRemoveCache(): IResponseObject
    {
        //TODO

        return $this->redirect('/admin/settings/cache/');
    }
}
