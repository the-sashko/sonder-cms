<?php

namespace Sonder\Controllers;

abstract class AdminBaseController extends BaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';
}
