<?php

namespace Sonder\CMS\Essentials;

abstract class AdminBaseController extends BaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';
}
