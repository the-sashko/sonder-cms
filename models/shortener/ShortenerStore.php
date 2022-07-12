<?php

namespace Sonder\Models\Shortener;

use Sonder\CMS\Essentials\BaseModelStore;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelStore;
use Sonder\Models\Shortener\Interfaces\IShortenerStore;
use Sonder\Models\Shortener\Interfaces\IShortenerValuesObject;

#[IModelStore]
#[IShortenerStore]
final class ShortenerStore extends BaseModelStore implements IShortenerStore
{
    final protected const SCOPE ='shortener';

    private const SHORTENER_TABLE = 'shortener';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getShortenerRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
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
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getShortenerRowsByPage(
        int  $page = 1,
        int  $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
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

        $offset = $limit * ($page - 1);

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
            $limit,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getShortenerRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
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
     * @return int
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
     */
    final public function getShortenerRowByCode(
        ?string $code = null,
        bool    $excludeRemoved = true,
        bool    $excludeInactive = true
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
     */
    final public function getShortenerRowByUrl(
        ?string $url = null,
        bool    $excludeRemoved = true,
        bool    $excludeInactive = true
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
     * @param IShortenerValuesObject|null $shortenerVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateShortener(
        ?IShortenerValuesObject $shortenerVO = null
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
     */
    final public function insertShortener(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(ShortenerStore::SHORTENER_TABLE, $row);
    }
}
