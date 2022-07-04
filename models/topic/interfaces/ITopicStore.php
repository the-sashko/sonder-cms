<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicStore extends IModelStore
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getTopicRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $slug
     * @return int|null
     */
    public function getTopicIdBySlug(?string $slug = null): ?int;

    /**
     * @param string|null $title
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getTopicRowByTitle(
        ?string $title = null,
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
    public function getTopicRowBySlug(
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
    public function updateTopicById(
        ?array $row = null,
        ?int $id = null
    ): bool;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteTopicById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreTopicById(?int $id = null): bool;

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getTopicRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getAllTopicRows(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getTopicRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $parentId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getTopicRowsByParentId(
        ?int $parentId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param ITopicValuesObject|null $topicVO
     * @return bool
     */
    public function insertOrUpdateTopic(
        ?ITopicValuesObject $topicVO = null
    ): bool;

    /**
     * @param array|null $row
     * @return bool
     */
    public function insertTopic(?array $row = null): bool;
}
