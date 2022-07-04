<?php

namespace Sonder\CMS\Essentials;

use Sonder\Interfaces\IController;

#[IController]
abstract class AdminBaseController extends BaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';
}
