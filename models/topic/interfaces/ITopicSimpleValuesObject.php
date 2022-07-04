<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return string|null
     */
    public function getTitle(): ?string;
}
