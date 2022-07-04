<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitValuesObject extends IModelValuesObject
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
    public function getIp(): string;

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
     * @param string|null $ip
     * @return void
     */
    public function setIp(?string $ip = null): void;
}
