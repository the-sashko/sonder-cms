<?php

namespace Sonder\Models\Tag\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface ITagModel extends IModel
{
    /**
     * @param string|null $slug
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITagValuesObject|null
     */
    public function getVOBySlug(
        ?string $slug = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITagValuesObject;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITagValuesObject|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITagValuesObject;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITagSimpleValuesObject|null
     */
    public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITagSimpleValuesObject;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @param bool $simplify
     * @return array|null
     */
    public function getTagsByPage(
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
    public function getAllTags(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getTagsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $articleId
     * @return array|null
     */
    public function getTagsByArticleId(?int $articleId = null): ?array;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeTagById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreTagById(?int $id = null): bool;

    /**
     * @param ITagForm $tagForm
     * @return bool
     */
    public function save(ITagForm $tagForm): bool;
}
