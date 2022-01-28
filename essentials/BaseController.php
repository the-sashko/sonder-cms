<?php

namespace Sonder\Controllers;

use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;

abstract class BaseController extends CoreController implements IController
{
    protected int $page = 1;

    public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        $page = $this->request->getUrlValue('page');

        $this->page = empty($page) ? 1 : $page;
    }
}
