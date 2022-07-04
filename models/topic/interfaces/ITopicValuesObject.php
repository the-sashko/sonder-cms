<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicValuesObject extends IModelValuesObject
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * @return int
     */
    public function getParentId(): int;

    /**
     * @return ITopicSimpleValuesObject|null
     */
    public function getParentVO(): ?ITopicSimpleValuesObject;

    /**
     * @return int
     */
    public function getViewsCount(): int;

    /**
     * @return string
     */
    public function getImageLink(): string;

    /**
     * @param string|null $title
     * @return void
     */
    public function setTitle(?string $title = null): void;

    /**
     * @param string|null $slug
     * @return void
     */
    public function setSlug(?string $slug = null): void;

    /**
     * @param int|null $parentId
     * @return void
     */
    public function setParentId(?int $parentId = null): void;

    /**
     * @param ITopicSimpleValuesObject|null $parentVO
     * @return void
     */
    public function setParentVO(
        ?ITopicSimpleValuesObject $parentVO = null
    ): void;

    /**
     * @return void
     */
    public function setViewsCount(): void;
}
