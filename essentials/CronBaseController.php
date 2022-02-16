<?php

namespace Sonder\Controllers;

use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;

abstract class CronBaseController extends BaseController implements IController
{
    public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        $this->id = null;
        $this->slug = null;
        $this->page = 0;
    }
}
