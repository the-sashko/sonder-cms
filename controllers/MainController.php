<?php

namespace Sonder\Controllers;

use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\ResponseObject;

final class MainController extends CoreController implements IController
{
    final public function displayIndex(): ResponseObject
    {
        return $this->render('index');

        //TODO
    }

    //TODO
}
