<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitAggregationValuesObject extends IModelValuesObject
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
     * @return int
     */
    public function getCount(): int;

    /**
     * @param int|null $articleId
     * @return void
     */
    public function setArticleId(?int $articleId = null): void;

    /**
     * @param int|null $topicId
     * @return void
     */
    public function setTopicId(?int $topicId = null): void;

    /**
     * @param int|null $tagId
     * @return void
     */
    public function setTagId(?int $tagId = null): void;

    /**
     * @param int|null $count
     * @return void
     */
    public function setCount(?int $count = null): void;
}
