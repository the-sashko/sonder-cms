<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getSummary(): ?string;
}
