<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormFileObject;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getParentId(): ?int;

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
     * @return IModelFormFileObject|null
     */
    public function getImage(): ?IModelFormFileObject;

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

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;

    /**
     * @param IModelFormFileObject|null $image
     * @return void
     */
    public function setImage(?IModelFormFileObject $image = null): void;
}
