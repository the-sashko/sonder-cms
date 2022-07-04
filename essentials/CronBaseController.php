<?php

namespace Sonder\CMS\Essentials;

use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Interfaces\IRequestObject;

abstract class CronBaseController extends BaseController
{
    /**
     * @param IRequestObject $request
     * @throws ConfigException
     * @throws ControllerException
     */
    public function __construct(IRequestObject $request)
    {
        parent::__construct($request);

        $this->id = null;
        $this->slug = null;
        $this->page = 0;
    }
}
