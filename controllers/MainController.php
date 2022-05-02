<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\BaseController;
use Sonder\Core\ResponseObject;

final class MainController extends BaseController
{
    /**
     * @area blog
     * @route /
     *
     * @return ResponseObject
     *
     * @throws Exception
     */
    final public function displayIndex(): ResponseObject
    {
        return $this->render('demo');

        //TODO
    }

    //TODO
}
