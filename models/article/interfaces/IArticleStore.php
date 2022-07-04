<?php

namespace Sonder\Models\Article\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface IArticleStore extends IModelStore
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $slug
     * @return int|null
     */
    public function getArticleIdBySlug(?string $slug = null): ?int;

    /**
     * @param string|null $title
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowByTitle(
        ?string $title = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $metaTitle
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowByMetaTitle(
        ?string $metaTitle = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $slug
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowBySlug(
        ?string $slug = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    public function updateArticleById(
        ?array $row = null,
        ?int $id = null
    ): bool;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteArticleById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreArticleById(?int $id = null): bool;

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getArticleRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $topicId
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowsByTopicId(
        ?int $topicId = null,
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getArticleRowsCountByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $tagId
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowsByTagId(
        ?int $tagId = null,
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getArticleRowsCountByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $userId
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getArticleRowsByUserId(
        ?int $userId = null,
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $userId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getArticleRowsCountByUserId(
        ?int $userId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param IArticleValuesObject|null $articleVO
     * @return bool
     */
    public function insertOrUpdateArticle(
        ?IArticleValuesObject $articleVO = null
    ): bool;

    /**
     * @param array|null $row
     * @return bool
     */
    public function insertArticle(?array $row = null): bool;

    /**
     * @param int|null $tagId
     * @param int|null $articleId
     * @return bool
     */
    public function insertArticle2TagRelation(
        ?int $tagId = null,
        ?int $articleId = null
    ): bool;

    /**
     * @param int|null $articleId
     * @return bool
     */
    public function deleteArticle2TagRelationsByArticleId(
        ?int $articleId = null
    ): bool;
}
