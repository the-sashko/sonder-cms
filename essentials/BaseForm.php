<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\ModelFormObject;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\IModelFormObject;

#[IModel]
abstract class BaseForm extends  ModelFormObject implements IModelFormObject
{
}
