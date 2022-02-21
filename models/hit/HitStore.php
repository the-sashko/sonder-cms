<?php

namespace Sonder\Models\Hit;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class HitStore extends ModelStore implements IModelStore
{
    const HITS_TABLE = 'hits';

    const HITS_BY_DAY_TABLE = 'hits_by_day';

    const HITS_BY_MONTH_TABLE = 'hits_by_month';

    const HITS_BY_YEAR_TABLE = 'hits_by_year';

    /**
     * @var string|null
     */
    public ?string $scope = 'hit';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getHitRowById(
        ?int $id = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"hits"."id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hits"."ddate" IS NULL OR "hits"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hits"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "hits".*
            FROM "%s" AS "hits"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            HitStore::HITS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param string $type
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAggregationRowById(
        string $type,
        ?int   $id = null,
        bool   $excludeRemoved = false,
        bool   $excludeInactive = false
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $table = $this->_getAggregationTable($type);

        $sqlWhere = sprintf('"%s"."id" = \'%d\'', $table, $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("%s"."ddate" IS NULL OR "%s"."ddate" < 1)
                ',
                $sqlWhere,
                $table,
                $table
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "%s"."is_active" = true
                ',
                $sqlWhere,
                $table
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            HitStore::HITS_BY_DAY_TABLE,
            $table,
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
    final public function updateHitById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            HitStore::HITS_TABLE,
            $row,
            $id
        );
    }

    /**
     * @param string $type
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function updateAggregationById(
        string $type,
        ?array $row = null,
        ?int   $id = null,
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        $table = $this->_getAggregationTable($type);

        return $this->updateRowById($table, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteHitById(
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

            return $this->updateHitById($row, $id);
        }

        return $this->deleteRowById(HitStore::HITS_TABLE, $id);
    }

    /**
     * @param string $type
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function deleteAggregationById(
        string $type,
        ?int   $id = null,
        bool   $isSoftDelete = true
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        $table = $this->_getAggregationTable($type);

        if ($isSoftDelete) {
            $row = [
                'ddate' => time(),
                'is_active' => false
            ];

            return $this->updateAggregationById($table, $row, $id);
        }

        return $this->deleteRowById($table, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreHitById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateHitById($row, $id);
    }

    /**
     * @param string $type
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function restoreAggregationById(
        string $type,
        ?int   $id = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        $table = $this->_getAggregationTable($type);

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateAggregationById($table, $row, $id);
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
    final public function getHitRowsByPage(
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
                ("hits"."ddate" IS NULL OR "hits"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hits"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s" AS "hits"
            WHERE %s
            ORDER BY "hits"."cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            HitStore::HITS_TABLE,
            $sqlWhere,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param string $type
     * @param int $page
     * @param int $itemsOnPage
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAggregationRowsByPage(
        string $type,
        int    $page = 1,
        int    $itemsOnPage = 10,
        bool   $excludeRemoved = false,
        bool   $excludeInactive = false
    ): ?array
    {
        $table = $this->_getAggregationTable($type);

        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("%s"."ddate" IS NULL OR "%s"."ddate" < 1)
                ',
                $sqlWhere,
                $table,
                $table
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "%s"."is_active" = true
                ',
                $sqlWhere,
                $table
            );
        }

        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s" AS "%d"
            WHERE %s
            ORDER BY "%s"."cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            HitStore::HITS_BY_DAY_TABLE,
            $table,
            $sqlWhere,
            $table,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getHitsRowsCount(
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hits"."ddate" IS NULL OR "hits"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hits"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("hits"."id") AS "count"
            FROM "%s" AS "hits"
            WHERE %s;
        ';

        $sql = sprintf($sql, HitStore::HITS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param string $type
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAggregationRowsCount(
        string $type,
        bool   $excludeRemoved = false,
        bool   $excludeInactive = false
    ): int
    {
        $table = $this->_getAggregationTable($type);

        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("%s"."ddate" IS NULL OR "%s"."ddate" < 1)
                ',
                $sqlWhere,
                $table,
                $table
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "%s"."is_active" = true
                ',
                $sqlWhere,
                $table
            );
        }

        $sql = '
            SELECT COUNT("%s"."id") AS "count"
            FROM "%s" AS "%s"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            $table,
            HitStore::HITS_BY_DAY_TABLE,
            $table,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param HitValuesObject|null $hitVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateHit(
        ?HitValuesObject $hitVO = null
    ): bool
    {
        $id = $hitVO->getId();

        if (empty($id)) {
            $hitVO->setCdate();

            return $this->insertHit($hitVO->exportRow());
        }

        $hitVO->setMdate();

        return $this->updateHitById($hitVO->exportRow(), $id);
    }

    /**
     * @param ModelValuesObject|null $aggregationVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateAggregation(
        ?ModelValuesObject $aggregationVO = null
    ): bool
    {
        $id = $aggregationVO->getId();
        $type = $aggregationVO->getType();

        if (empty($id)) {
            $aggregationVO->setCdate();

            return $this->insertAggregation($type, $aggregationVO->exportRow());
        }

        $aggregationVO->setMdate();

        $row = $aggregationVO->exportRow();

        return $this->updateAggregationById($type, $row, $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertHit(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(HitStore::HITS_TABLE, $row);
    }

    /**
     * @param string $type
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertAggregation(
        string $type,
        ?array $row = null
    ): bool
    {
        if (empty($row)) {
            return false;
        }

        $table = $this->_getAggregationTable($type);

        return $this->addRow($table, $row);
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sum = $this->_getCountFromHitsByArticleId(
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByArticleId(
            'day',
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByArticleId(
            'month',
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByArticleId(
            'year',
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        return $sum;
    }

    /**
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCountByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sum = $this->_getCountFromHitsByTopicId(
            $topicId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByTopicId(
            'day',
            $topicId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByTopicId(
            'month',
            $topicId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByTopicId(
            'year',
            $topicId,
            $excludeRemoved,
            $excludeInactive
        );

        return $sum;
    }

    /**
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCountByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sum = $this->_getCountFromHitsByTagId(
            $tagId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByTagId(
            'day',
            $tagId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByTagId(
            'month',
            $tagId,
            $excludeRemoved,
            $excludeInactive
        );

        $sum += $this->_getCountFromAggregationByTagId(
            'year',
            $tagId,
            $excludeRemoved,
            $excludeInactive
        );

        return $sum;
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _getCountFromHitsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sqlWhere = sprintf('"hits"."article_id" = %d', $articleId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hits"."ddate" IS NULL OR "hits"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hits"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("hits"."id") AS "count"
            FROM "%s" AS "hits"
            WHERE %s
            GROUP BY "hits"."article_id";
        ';

        $sql = sprintf($sql, HitStore::HITS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param string $type
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getCountFromAggregationByArticleId(
        string $type,
        ?int   $articleId = null,
        bool   $excludeRemoved = false,
        bool   $excludeInactive = false
    ): int
    {
        $table = $this->_getAggregationTable($type);

        $sqlWhere = sprintf(
            '"%s"."article_id" = %d',
            $table,
            $articleId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("%s"."ddate" IS NULL OR "%s"."ddate" < 1)
                ',
                $sqlWhere,
                $table,
                $table
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "%s"."is_active" = true
                ',
                $sqlWhere,
                $table
            );
        }

        $sql = '
            SELECT SUM("%s"."count") AS "count"
            FROM "%s" AS "%s"
            WHERE %s
            GROUP BY "%s"."article_id";
        ';

        $sql = sprintf($sql, $table, $table, $table, $sqlWhere, $table);

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _getCountFromHitsByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sqlWhere = sprintf('"hits"."topic_id" = %d', $topicId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hits"."ddate" IS NULL OR "hits"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hits"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("hits"."id") AS "count"
            FROM "%s" AS "hits"
            WHERE %s
            GROUP BY "hits"."topic_id";
        ';

        $sql = sprintf($sql, HitStore::HITS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param string $type
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getCountFromAggregationByTopicId(
        string $type,
        ?int   $topicId = null,
        bool   $excludeRemoved = false,
        bool   $excludeInactive = false
    ): int
    {
        $table = $this->_getAggregationTable($type);

        $sqlWhere = sprintf('"%s"."topic_id" = %d', $table, $topicId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("%s"."ddate" IS NULL OR "%s"."ddate" < 1)
                ',
                $sqlWhere,
                $table,
                $table
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "%s"."is_active" = true
                ',
                $sqlWhere,
                $table
            );
        }

        $sql = '
            SELECT SUM("%s"."count") AS "count"
            FROM "%s" AS "%s"
            WHERE %s
            GROUP BY "%s"."topic_id";
        ';

        $sql = sprintf($sql, $table, $table, $table, $sqlWhere, $table);

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _getCountFromHitsByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $sqlWhere = sprintf('"hits"."tag_id" = %d', $tagId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hits"."ddate" IS NULL OR "hits"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hits"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("hits"."id") AS "count"
            FROM "%s" AS "hits"
            WHERE %s
            GROUP BY "hits"."tag_id";
        ';

        $sql = sprintf($sql, HitStore::HITS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param string $type
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getCountFromAggregationByTagId(
        string $type,
        ?int   $tagId = null,
        bool   $excludeRemoved = false,
        bool   $excludeInactive = false
    ): int
    {
        $table = $this->_getAggregationTable($type);

        $sqlWhere = sprintf('"%s"."tag_id" = %d', $table, $tagId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("%s"."ddate" IS NULL OR "%s"."ddate" < 1)
                ',
                $sqlWhere,
                $table,
                $table
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "%s"."is_active" = true
                ',
                $sqlWhere,
                $table
            );
        }

        $sql = '
            SELECT SUM("%s"."count") AS "count"
            FROM "%s" AS "%s"
            WHERE %s
            GROUP BY "%s"."tag_id";
        ';

        $sql = sprintf($sql, $table, $table, $table, $sqlWhere, $table);

        return (int)$this->getOne($sql);
    }

    /**
     * @param string $aggregationType
     * @return string
     * @throws Exception
     */
    private function _getAggregationTable(string $aggregationType): string
    {
        $table = null;

        switch ($aggregationType) {
            case 'day':
                $table = HitStore::HITS_BY_DAY_TABLE;
                break;
            case 'month':
                $table = HitStore::HITS_BY_MONTH_TABLE;
                break;
            case 'year':
                $table = HitStore::HITS_BY_YEAR_TABLE;
                break;
        }

        if (empty($table)) {
            throw new Exception('Invalid Aggregation Type');
        }

        return $table;
    }
}
