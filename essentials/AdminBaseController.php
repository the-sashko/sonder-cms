<?php

namespace Sonder\Controllers;

use Sonder\Core\Interfaces\IController;

abstract class AdminBaseController extends BaseController implements IController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';
}
