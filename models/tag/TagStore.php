<?php

namespace Sonder\Models\Tag;

use Exception;
use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class TagStore extends ModelStore implements IModelStore
{
    const TAGS_TABLE = 'tags';

    /**
     * @var string|null
     */
    public ?string $scope = 'tag';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTagRowById(
        ?int $id = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TagStore::TAGS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $slug
     * @return int|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTagIdBySlug(?string $slug = null): ?int
    {
        if (empty($slug)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "slug" = \'%s\'', $slug);

        $sql = '
            SELECT "id"
            FROM "%s"
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TagStore::TAGS_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param string|null $title
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTagRowByTitle(
        ?string $title = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($title)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "title" = \'%s\'', $title);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TagStore::TAGS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $slug
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTagRowBySlug(
        ?string $slug = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($slug)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "slug" = \'%s\'', $slug);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TagStore::TAGS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateTagById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(TagStore::TAGS_TABLE, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteTagById(
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

            return $this->updateTagById($row, $id);
        }

        return $this->deleteRowById(TagStore::TAGS_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreTagById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateTagById($row, $id);
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
    final public function getTagRowsByPage(
        int  $page = 1,
        int  $itemsOnPage = 10,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $sqlWhere = 'WHERE true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s"
            %s
            ORDER BY "cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            TagStore::TAGS_TABLE,
            $sqlWhere,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getAllTagRows(
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $sqlWhere = 'WHERE true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            %s
            ORDER BY "cdate" DESC;
        ';

        $sql = sprintf($sql, TagStore::TAGS_TABLE, $sqlWhere);

        return $this->getRows($sql);
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTagRowsCount(): int
    {
        $sql = '
            SELECT COUNT(*) AS "count"
            FROM "%s";
        ';

        $sql = sprintf(
            $sql,
            TagStore::TAGS_TABLE,
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param TagValuesObject|null $tagVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateTag(
        ?TagValuesObject $tagVO = null
    ): bool
    {
        $id = $tagVO->getId();

        if (empty($id)) {
            $tagVO->setCdate();

            return $this->insertTag($tagVO->exportRow());
        }

        $tagVO->setMdate();

        return $this->updateTagById($tagVO->exportRow(), $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertTag(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(TagStore::TAGS_TABLE, $row);
    }
}