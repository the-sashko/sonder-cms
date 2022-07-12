<?php

namespace Sonder\Models\Comment;

use Sonder\CMS\Essentials\BaseModelStore;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelStore;
use Sonder\Models\Comment\Interfaces\ICommentStore;
use Sonder\Models\Comment\Interfaces\ICommentValuesObject;

#[IModelStore]
#[ICommentStore]
final class CommentStore extends BaseModelStore implements ICommentStore
{
    final protected const SCOPE ='comment';

    private const COMMENTS_TABLE = 'comments';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getCommentRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"comments"."id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "comments"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param int|null $parentId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getCommentRowsByParentId(
        ?int $parentId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($parentId)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"comments"."parent_id" = \'%d\'',
            $parentId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "comments"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return $this->getRows($sql);
    }

    /**
     * @param int|null $userId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getCommentRowsByUserId(
        ?int $userId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($userId)) {
            return null;
        }

        $sqlWhere = sprintf('"comments"."user_id" = \'%d\'', $userId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "comments"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return $this->getRows($sql);
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getCommentRowsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($articleId)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"comments"."article_id" = \'%d\'',
            $articleId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT *
            FROM "%s" AS "comments"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return $this->getRows($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateCommentById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            CommentStore::COMMENTS_TABLE,
            $row,
            $id
        );
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteCommentById(
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

            return $this->updateCommentById($row, $id);
        }

        return $this->deleteRowById(CommentStore::COMMENTS_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreCommentById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateCommentById($row, $id);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getCommentRowsByPage(
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
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $limit * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s" AS "comments"
            WHERE %s
            ORDER BY "comments"."cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
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
    final public function getCommentRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("comments"."id") AS "count"
            FROM "%s" AS "comments"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCommentRowsCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        if (empty($articleId)) {
            return 0;
        }

        $sqlWhere = sprintf(
            '"comments"."article" = \'%d\'',
            $articleId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("comments"."id") AS "count"
            FROM "%s" AS "comments"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $userId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCommentRowsCountByUserId(
        ?int $userId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        if (empty($userId)) {
            return 0;
        }

        $sqlWhere = sprintf(
            '"comments"."user_id" = \'%d\'',
            $userId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("comments"."ddate" IS NULL OR "comments"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "comments"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("comments"."id") AS "count"
            FROM "%s" AS "comments"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            CommentStore::COMMENTS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param ICommentValuesObject|null $commentVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateComment(
        ?ICommentValuesObject $commentVO = null
    ): bool
    {
        $id = $commentVO->getId();

        if (empty($id)) {
            $commentVO->setCdate();

            return $this->insertComment($commentVO->exportRow());
        }

        $commentVO->setMdate();

        return $this->updateCommentById($commentVO->exportRow(), $id);
    }

    /**
     * @param array|null $row
     * @return bool
     */
    final public function insertComment(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(CommentStore::COMMENTS_TABLE, $row);
    }
}
