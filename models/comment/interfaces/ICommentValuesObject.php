<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Models\User\Interfaces\IUserValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentValuesObject extends IModelValuesObject
{
    /**
     * @return int|null
     */
    public function getParentId(): ?int;

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
     * @return string|null
     */
    public function getUserIp(): ?string;

    /**
     * @return IUserValuesObject|null
     */
    public function getUserVO(): ?IUserValuesObject;

    /**
     * @return string
     */
    public function getText(): string;

    /**
     * @return string
     */
    public function getHtml(): string;

    /**
     * @param int|null $parentId
     * @return void
     */
    public function setParentId(?int $parentId = null): void;

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
     * @param string|null $userIp
     * @return void
     */
    public function setUserIp(?string $userIp = null): void;

    /**
     * @param IUserValuesObject|null $userVO
     * @return void
     */
    public function setUserVO(?IUserValuesObject $userVO = null): void;

    /**
     * @param string|null $text
     * @return void
     */
    public function setText(?string $text = null): void;

    /**
     * @param string|null $html
     * @return void
     */
    public function setHtml(?string $html = null): void;
}
