<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\ModelStore;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
abstract class BaseModelStore extends ModelStore implements IModelStore
{
}