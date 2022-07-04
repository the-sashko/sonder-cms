<?php

namespace Sonder\Models\Hit\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IHitForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

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
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @return int|null
     */
    public function getCount(): ?int;

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
     * @param int|null $count
     * @return void
     */
    public function setCount(?int $count = null): void;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;
}
