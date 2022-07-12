<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException as CoreBaseException;

#[ICoreException]
abstract class BaseException
    extends CoreBaseException
    implements ICoreException
{
}
