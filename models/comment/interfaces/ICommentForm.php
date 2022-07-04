<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentForm extends IModelFormObject
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
    public function getText(): ?string;

    /**
     * @return int|null
     */
    public function getArticleId(): ?int;

    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @return string|null
     */
    public function getUserName(): ?string;

    /**
     * @return string|null
     */
    public function getUserEmail(): ?string;

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
     * @param int|null $parentId
     * @return void
     */
    public function setParentId(?int $parentId = null): void;

    /**
     * @param string|null $text
     * @return void
     */
    public function setText(?string $text = null): void;

    /**
     * @param int|null $articleId
     * @return void
     */
    public function setArticleId(?int $articleId = null): void;

    /**
     * @param int|null $userId
     * @return void
     */
    public function setUserId(?int $userId = null): void;

    /**
     * @param string|null $userName
     * @return void
     */
    public function setUserName(?string $userName = null): void;

    /**
     * @param string|null $userEmail
     * @return void
     */
    public function setUserEmail(?string $userEmail = null): void;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;
}
