<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentStore extends IModelStore
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $parentId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentRowsByParentId(
        ?int $parentId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $userId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentRowsByUserId(
        ?int $userId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentRowsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    public function updateCommentById(
        ?array $row = null,
        ?int $id = null
    ): bool;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteCommentById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreCommentById(?int $id = null): bool;

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentRowsByPage(
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
    public function getCommentRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getCommentRowsCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $userId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getCommentRowsCountByUserId(
        ?int $userId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param ICommentValuesObject|null $commentVO
     * @return bool
     */
    public function insertOrUpdateComment(
        ?ICommentValuesObject $commentVO = null
    ): bool;
}
