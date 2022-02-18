<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\ResponseObject;

final class AdminCacheController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/cache/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayCache(): ResponseObject
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
    final public function displayRemoveCache(): ResponseObject
    {
        //TODO

        return $this->redirect('/admin/settings/cache/');
    }
}
