<?php

namespace Sonder\Models\Topic\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITopicModel extends IModel
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITopicValuesObject|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITopicValuesObject;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITopicSimpleValuesObject|null
     */
    public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITopicSimpleValuesObject;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @param bool $simplify
     * @return array|null
     */
    public function getTopicsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true,
        bool $simplify = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getAllTopics(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getTopicsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeTopicById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeTopicImageById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreTopicById(?int $id = null): bool;

    /**
     * @param ITopicForm $topicForm
     * @return bool
     */
    public function save(ITopicForm $topicForm): bool;
}
