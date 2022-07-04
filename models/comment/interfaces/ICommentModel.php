<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentModel extends IModel
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ICommentValuesObject|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ICommentValuesObject;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ICommentSimpleValuesObject|null
     */
    public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ICommentSimpleValuesObject;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getCommentsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getCommentsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getArticlesPageCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $userId
     * @param int $page
     * @return array|null
     */
    public function getCommentsByUserId(
        ?int $userId = null,
        int $page = 1
    ): ?array;

    /**
     * @param int|null $userId
     * @return int
     */
    public function getCommentsPageCountByUserId(?int $userId = null): int;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeCommentById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreCommentById(?int $id = null): bool;

    /**
     * @param ICommentForm $commentForm
     * @return bool
     */
    public function save(ICommentForm $commentForm): bool;
}
