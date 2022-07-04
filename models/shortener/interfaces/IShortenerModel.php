<?php

namespace Sonder\Models\Shortener\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IShortenerModel extends IModel
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IShortenerValuesObject|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IShortenerValuesObject;

    /**
     * @param string|null $code
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IShortenerValuesObject|null
     */
    public function getVOByCode(
        ?string $code = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IShortenerValuesObject;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getShortenerVOsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getShortLinksPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeShortenerById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreShortenerById(?int $id = null): bool;

    /**
     * @param IShortenerForm $shortenerForm
     * @return bool
     */
    public function save(IShortenerForm $shortenerForm): bool;
}
