<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\IResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminTaxonomyController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/taxonomy/
     * @no_cache true
     *
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTaxonomy(): IResponseObject
    {
        $this->assign([
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Taxonomy'
            ]
        ]);

        return $this->render('taxonomy');
    }
}
