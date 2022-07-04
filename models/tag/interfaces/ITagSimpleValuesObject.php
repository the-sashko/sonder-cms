<?php

namespace Sonder\Models\Tag\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITagSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return string|null
     */
    public function getTitle(): ?string;
}
