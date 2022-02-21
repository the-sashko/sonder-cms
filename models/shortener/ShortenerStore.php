<?php

namespace Sonder\Models\Shortener;

use Exception;
use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class ShortenerStore extends ModelStore implements IModelStore
{
    const SHORTENER_TABLE = 'shortener';

    /**
     * @var string|null
     */
    public ?string $scope = 'shortener';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getShortenerRowById(
        ?int $id = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"shortener"."id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("shortener"."ddate" IS NULL OR "shortener"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "shortener"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "shortener"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            ShortenerStore::SHORTENER_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateShortenerById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            ShortenerStore::SHORTENER_TABLE,
            $row,
            $id
        );
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteShortenerById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        if ($isSoftDelete) {
            $row = [
                'ddate' => time(),
                'is_active' => false
            ];

            return $this->updateShortenerById($row, $id);
        }

        return $this->deleteRowById(ShortenerStore::SHORTENER_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreShortenerById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateShortenerById($row, $id);
    }

    /**
     * @param int $page
     * @param int $itemsOnPage
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getShortenerRowsByPage(
        int  $page = 1,
        int  $itemsOnPage = 10,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("shortener"."ddate" IS NULL OR "shortener"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "shortener"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s" AS "shortener"
            WHERE %s
            ORDER BY "shortener"."cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            ShortenerStore::SHORTENER_TABLE,
            $sqlWhere,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getShortenerRowsCount(
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("shortener"."ddate" IS NULL OR "shortener"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "shortener"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("shortener"."id") AS "count"
            FROM "%s" AS "shortener"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ShortenerStore::SHORTENER_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getMaxId(): int
    {
        $sql = '
            SELECT MAX("shortener"."id") AS "max"
            FROM "%s" AS "shortener"
            WHERE true;
        ';

        $sql = sprintf($sql, ShortenerStore::SHORTENER_TABLE);

        return (int)$this->getOne($sql);
    }

    /**
     * @param string|null $code
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getShortenerRowByCode(
        ?string $code = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($code)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"shortener"."code" = \'%s\'',
            $code
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("shortener"."ddate" IS NULL OR "shortener"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "shortener"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "shortener"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ShortenerStore::SHORTENER_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param string|null $url
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getShortenerRowByUrl(
        ?string $url = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($url)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"shortener"."url" = \'%s\'',
            $url
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("shortener"."ddate" IS NULL OR "shortener"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "shortener"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "shortener"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ShortenerStore::SHORTENER_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param ShortenerValuesObject|null $shortenerVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateShortener(
        ?ShortenerValuesObject $shortenerVO = null
    ): bool
    {
        $id = $shortenerVO->getId();

        if (empty($id)) {
            $shortenerVO->setCdate();

            return $this->insertShortener($shortenerVO->exportRow());
        }

        $shortenerVO->setMdate();

        return $this->updateShortenerById($shortenerVO->exportRow(), $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertShortener(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(ShortenerStore::SHORTENER_TABLE, $row);
    }
}
