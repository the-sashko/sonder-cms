<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitAggregationSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return int|null
     */
    public function getArticleId(): ?int;

    /**
     * @return int|null
     */
    public function getTopicId(): ?int;

    /**
     * @return int|null
     */
    public function getTagId(): ?int;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return int
     */
    public function getCount(): int;
}
