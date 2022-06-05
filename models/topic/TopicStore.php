<?php

namespace Sonder\Models\Topic;

use Exception;
use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class TopicStore extends ModelStore implements IModelStore
{
    const TOPICS_TABLE = 'topics';

    /**
     * @var string|null
     */
    public ?string $scope = 'topic';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTopicRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"id" = \'%d\'', $id);

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
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $slug
     * @return int|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTopicIdBySlug(?string $slug = null): ?int
    {
        if (empty($slug)) {
            return null;
        }

        $sqlWhere = sprintf('"slug" = \'%s\'', $slug);

        $sql = '
            SELECT "id"
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

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
    final public function getTopicRowByTitle(
        ?string $title = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = true,
        bool    $excludeInactive = true
    ): ?array
    {
        if (empty($title)) {
            return null;
        }

        $sqlWhere = sprintf('"title" = \'%s\'', $title);

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
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

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
    final public function getTopicRowBySlug(
        ?string $slug = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = true,
        bool    $excludeInactive = true
    ): ?array
    {
        if (empty($slug)) {
            return null;
        }

        $sqlWhere = sprintf('"slug" = \'%s\'', $slug);

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
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateTopicById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(TopicStore::TOPICS_TABLE, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteTopicById(
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

            return $this->updateTopicById($row, $id);
        }

        return $this->deleteRowById(TopicStore::TOPICS_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreTopicById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateTopicById($row, $id);
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
    final public function getTopicRowsByPage(
        int  $page = 1,
        int  $itemsOnPage = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $sqlWhere = 'true';

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
            WHERE %s
            ORDER BY "cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            TopicStore::TOPICS_TABLE,
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
    final public function getAllTopicRows(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $sqlWhere = 'true';

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
            WHERE %s
            ORDER BY "cdate" DESC;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

        return $this->getRows($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTopicRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        $sqlWhere = 'true';

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
            SELECT COUNT("id") AS "count"
            FROM "%s"
            WHERE %s;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $parentId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTopicRowsByParentId(
        ?int $parentId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($parentId)) {
            return null;
        }

        $sqlWhere = sprintf('"parent_id" = \'%d\'', $parentId);

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
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, TopicStore::TOPICS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param TopicValuesObject|null $topicVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateTopic(
        ?TopicValuesObject $topicVO = null
    ): bool
    {
        $id = $topicVO->getId();

        if (empty($id)) {
            $topicVO->setCdate();

            return $this->insertTopic($topicVO->exportRow());
        }

        $topicVO->setMdate();

        return $this->updateTopicById($topicVO->exportRow(), $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertTopic(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(TopicStore::TOPICS_TABLE, $row);
    }
}
