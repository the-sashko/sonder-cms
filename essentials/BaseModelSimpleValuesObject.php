<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
abstract class BaseModelSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IModelSimpleValuesObject
{
}
