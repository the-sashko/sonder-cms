<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\CronBaseController;
use Sonder\Interfaces\IController;

#[IController]
final class CronHitController extends CronBaseController
{
    /**
     * @return void
     */
    final public function jobAggregate(): void
    {
        //TODO
    }
}
