<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\RequestObject;

abstract class CronBaseController extends BaseController
{
    public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        $this->id = null;
        $this->slug = null;
        $this->page = 0;
    }
}
