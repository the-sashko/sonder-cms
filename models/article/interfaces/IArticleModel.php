<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleModel extends IModel
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleValuesObject|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleValuesObject;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleSimpleValuesObject|null
     */
    public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleSimpleValuesObject;

    /**
     * @param string|null $slug
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleValuesObject|null
     */
    public function getVOBySlug(
        ?string $slug = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleValuesObject;

    /**
     * @param string|null $slug
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleSimpleValuesObject|null
     */
    public function getSimpleVOBySlug(
        ?string $slug = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleSimpleValuesObject;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @param bool $simplify
     * @return array|null
     */
    public function getArticlesByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true,
        bool $simplify = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getArticlesPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $topicId
     * @param int $page
     * @return array|null
     */
    public function getArticlesByTopicId(
        ?int $topicId = null,
        int $page = 1
    ): ?array;

    /**
     * @param int|null $topicId
     * @return int
     */
    public function getArticlesPageCountByTopicId(?int $topicId = null): int;

    /**
     * @param int|null $tagId
     * @param int $page
     * @return array|null
     */
    public function getArticlesByTagId(
        ?int $tagId = null,
        int $page = 1
    ): ?array;

    /**
     * @param int|null $tagId
     * @return int
     */
    public function getArticlesPageCountByTagId(
        ?int $tagId = null
    ): int;

    /**
     * @param int|null $userId
     * @param int $page
     * @return array|null
     */
    public function getArticlesByUserId(
        ?int $userId = null,
        int $page = 1
    ): ?array;

    /**
     * @param int|null $userId
     * @return int
     */
    public function getArticlesPageCountByUserId(?int $userId = null): int;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeArticleById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreArticleById(?int $id = null): bool;

    /**
     * @param IArticleForm $articleForm
     * @return bool
     */
    public function save(IArticleForm $articleForm): bool;
}
