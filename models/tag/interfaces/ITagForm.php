<?php

namespace Sonder\Models\Tag\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITagForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id = null): void;

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
}
