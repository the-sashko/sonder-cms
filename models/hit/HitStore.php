<?php

namespace Sonder\Models\Hit;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Models\Hit\Interfaces\IHitAggregationValuesObject;
use Sonder\Models\Hit\Interfaces\IHitStore;
use Sonder\Models\Hit\Interfaces\IHitValuesObject;

#[IModelStore]
#[IHitStore]
final class HitStore extends ModelStore implements IHitStore
{
    final protected const SCOPE = 'hit';

    private const HITS_TABLE = 'hits';

    private const HITS_AGGREGATIONS_TABLE = 'hit_aggregations';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getHitRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
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
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getAggregationRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"hit_aggregations"."id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                (
                    "hit_aggregations"."ddate" IS NULL OR
                    "hit_aggregations"."ddate" < 1
                )
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hit_aggregations"."is_active" = true
                ',
                $sqlWhere
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
            HitStore::HITS_AGGREGATIONS_TABLE,
            HitStore::HITS_AGGREGATIONS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateHitById(
        ?array $row = null,
        ?int $id = null
    ): bool {
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
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateAggregationById(
        ?array $row = null,
        ?int $id = null,
    ): bool {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            HitStore::HITS_AGGREGATIONS_TABLE,
            $row,
            $id
        );
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteHitById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool {
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
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteAggregationById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool {
        if (empty($id)) {
            return false;
        }

        if ($isSoftDelete) {
            $row = [
                'ddate' => time(),
                'is_active' => false
            ];

            return $this->updateAggregationById($row, $id);
        }

        return $this->deleteRowById(HitStore::HITS_AGGREGATIONS_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreHitById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => null,
            'is_active' => true
        ];

        return $this->updateHitById($row, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreAggregationById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => null,
            'is_active' => true
        ];

        return $this->updateAggregationById($row, $id);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getHitRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
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

        $offset = $limit * ($page - 1);

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
            $limit,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getAggregationRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hit_aggregations"."ddate" IS NULL OR "hit_aggregations"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hit_aggregations"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $limit * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s" AS "hit_aggregations"
            WHERE %s
            ORDER BY "hit_aggregations"."cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            HitStore::HITS_AGGREGATIONS_TABLE,
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
    final public function getHitsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
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
            SELECT
                COUNT("hits"."id") AS "count"
            FROM "%s" AS "hits"
            WHERE %s;
        ';

        $sql = sprintf($sql, HitStore::HITS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getHitAggregationsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("hit_aggregations"."ddate" IS NULL OR "hit_aggregations"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hit_aggregations"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT
                COUNT("hit_aggregations"."id") AS "count"
            FROM "%s" AS "hit_aggregations"
            WHERE %s;
        ';

        $sql = sprintf($sql, HitStore::HITS_AGGREGATIONS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param IHitValuesObject|null $hitVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateHit(
        ?IHitValuesObject $hitVO = null
    ): bool {
        $id = $hitVO->getId();

        if (empty($id)) {
            $hitVO->setCdate();

            return $this->insertHit($hitVO->exportRow());
        }

        $hitVO->setMdate();

        return $this->updateHitById($hitVO->exportRow(), $id);
    }

    /**
     * @param IHitAggregationValuesObject|null $hitAggregationVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateAggregation(
        ?IHitAggregationValuesObject $hitAggregationVO = null
    ): bool {
        $id = $hitAggregationVO->getId();

        if (empty($id)) {
            $hitAggregationVO->setCdate();

            return $this->insertAggregation($hitAggregationVO->exportRow());
        }

        $hitAggregationVO->setMdate();

        $row = $hitAggregationVO->exportRow();

        return $this->updateAggregationById($row, $id);
    }

    /**
     * @param array|null $row
     * @return bool
     */
    final public function insertHit(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(HitStore::HITS_TABLE, $row);
    }

    /**
     * @param array|null $row
     * @return bool
     */
    final public function insertAggregation(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(HitStore::HITS_AGGREGATIONS_TABLE, $row);
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $sqlWhere = sprintf('"hit_aggregations"."article_id" = %d', $articleId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                (
                    "hit_aggregations"."ddate" IS NULL OR
                    "hit_aggregations"."ddate" < 1
                )
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hit_aggregations"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "hit_aggregations"."count" AS "count"
            FROM "%s" AS "hit_aggregations"
            WHERE %s;
        ';

        $sql = sprintf($sql, HitStore::HITS_AGGREGATIONS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql) + $this->_getCountOfHitsByArticleId(
                $articleId,
                $excludeRemoved,
                $excludeInactive
            );
    }

    /**
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCountByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $sqlWhere = sprintf('"hit_aggregations"."topic_id" = %d', $topicId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                (
                    "hit_aggregations"."ddate" IS NULL OR "hit_aggregations"."ddate" < 1
                )
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hit_aggregations"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "hit_aggregations"."count" AS "count"
            FROM "%s" AS "hit_aggregations"
            WHERE %s;
        ';

        $sql = sprintf($sql, HitStore::HITS_AGGREGATIONS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql) + $this->_getCountOfHitsByTopicId(
                $topicId,
                $excludeRemoved,
                $excludeInactive
            );
    }

    /**
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCountByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $sqlWhere = sprintf('"hit_aggregations"."tag_id" = %d', $tagId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                (
                    "hit_aggregations"."ddate" IS NULL OR "hit_aggregations"."ddate" < 1
                )
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "hit_aggregations"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "hit_aggregations"."count" AS "count"
            FROM "%s" AS "hit_aggregations"
            WHERE %s;
        ';

        $sql = sprintf($sql, HitStore::HITS_AGGREGATIONS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql) + $this->_getCountOfHitsByTagId(
                $tagId,
                $excludeRemoved,
                $excludeInactive
            );
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    private function _getCountOfHitsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
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
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    private function _getCountOfHitsByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
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
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    private function _getCountOfHitsByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
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
}
