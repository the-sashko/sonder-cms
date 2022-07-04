<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IResponseObject;

#[IController]
final class AdminTaxonomyController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/taxonomy/
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
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
