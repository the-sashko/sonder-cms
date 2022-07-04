<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormFileObject;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleForm extends IModelFormObject
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
     * @return IModelFormFileObject|null
     */
    public function getImage(): ?IModelFormFileObject;

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
     * @return int|null
     */
    public function getTopicId(): ?int;

    /**
     * @return int|null
     */
    public function getUserId(): ?int;

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
     * @param int|null $topicId
     * @return void
     */
    public function setTopicId(?int $topicId = null): void;

    /**
     * @param int|null $userId
     * @return void
     */
    public function setUserId(?int $userId = null): void;

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
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;
}
