<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;

final class ArticleController extends BaseController
{
    /**
     * @area blog
     * @route /p/([a-z]+)/
     * @url_params slug=$1
     *
     * @return ResponseObject
     *
     * @throws Exception
     */
    final public function displaySingle(): ResponseObject
    {
        return $this->render('index');

        //TODO
    }

    //TODO
}
