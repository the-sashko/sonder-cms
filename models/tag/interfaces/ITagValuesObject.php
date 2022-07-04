<?php

namespace Sonder\Models\Tag\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITagValuesObject extends IModelValuesObject
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
    public function getViewsCount(): int;

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
     * @return void
     */
    public function setViewsCount(): void;
}
