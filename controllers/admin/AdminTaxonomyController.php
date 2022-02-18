<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\ResponseObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminTaxonomyController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/taxonomy/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTaxonomy(): ResponseObject
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
