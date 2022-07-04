<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicSimpleValuesObject;
use Sonder\Models\User\Interfaces\IUserSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleValuesObject extends IModelValuesObject
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
     * @return string|null
     */
    public function getImageDir(): ?string;

    /**
     * @return string|null
     */
    public function getSummary(): ?string;

    /**
     * @return string|null
     */
    public function getText(): ?string;

    /**
     * @return string|null
     */
    public function getHtml(): ?string;

    /**
     * @return int
     */
    public function getTopicId(): int;

    /**
     * @return ITopicSimpleValuesObject|null
     */
    public function getTopicVO(): ?ITopicSimpleValuesObject;

    /**
     * @return int
     */
    public function getUserId(): int;

    /**
     * @return IUserSimpleValuesObject|null
     */
    public function getUserVO(): ?IUserSimpleValuesObject;

    /**
     * @return string|null
     */
    public function getMetaTitle(): ?string;

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string;

    /**
     * @return array|null
     */
    public function getTags(): ?array;

    /**
     * @return array|null
     */
    public function getComments(): ?array;

    /**
     * @return array|null
     */
    public function getTagIds(): ?array;

    /**
     * @param string $size
     * @return string
     */
    public function getImageLink(string $size): string;

    /**
     * @return int
     */
    public function getViewsCount(): int;

    /**
     * @return string|null
     */
    public function getAdminViewLink(): ?string;

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
     * @param string|null $imageDir
     * @return void
     */
    public function setImageDir(?string $imageDir = null): void;

    /**
     * @param string|null $summary
     * @return void
     */
    public function setSummary(?string $summary = null): void;

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

    /**
     * @param int|null $topicId
     * @return void
     */
    public function setTopicId(?int $topicId = null): void;

    /**
     * @param ITopicSimpleValuesObject|null $topicVO
     * @return void
     */
    public function setTopicVO(?ITopicSimpleValuesObject $topicVO = null): void;

    /**
     * @param int|null $userId
     * @return void
     */
    public function setUserId(?int $userId = null): void;

    /**
     * @param IUserSimpleValuesObject|null $userVO
     * @return void
     */
    public function setUserVO(?IUserSimpleValuesObject $userVO = null): void;

    /**
     * @param string|null $metaTitle
     * @return void
     */
    public function setMetaTitle(?string $metaTitle = null): void;

    /**
     * @param string|null $metaDescription
     * @return void
     */
    public function setMetaDescription(?string $metaDescription = null): void;

    /**
     * @param array|null $tags
     * @return void
     */
    public function setTags(?array $tags = null): void;

    /**
     * @param array|null $comments
     * @return void
     */
    public function setComments(?array $comments = null): void;

    /**
     * @return void
     */
    public function setViewsCount(): void;
}
