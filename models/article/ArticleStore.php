<?php

namespace Sonder\Models\Article;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Models\Article\Interfaces\IArticleStore;
use Sonder\Models\Article\Interfaces\IArticleValuesObject;

#[IModelStore]
#[IArticleStore]
final class ArticleStore extends ModelStore implements IArticleStore
{
    final protected const SCOPE = 'article';

    private const ARTICLES_TABLE = 'articles';
    private const TOPICS_TABLE = 'topics';
    private const TAG_TO_ARTICLE_TABLE = 'tag2article';
    private const TAGS_TABLE = 'tags';
    private const USERS_TABLE = 'users';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"articles"."id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param string|null $slug
     * @return int|null
     */
    final public function getArticleIdBySlug(?string $slug = null): ?int
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

        $sql = sprintf($sql, ArticleStore::ARTICLES_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param string|null $title
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowByTitle(
        ?string $title = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($title)) {
            return null;
        }

        $sqlWhere = sprintf('"articles"."title" = \'%s\'', $title);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "articles"."id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    final public function getArticleRowByMetaTitle(
        ?string $metaTitle = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($metaTitle)) {
            return null;
        }

        $sqlWhere = sprintf('"articles"."meta_title" = \'%s\'', $metaTitle);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "articles"."id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param string|null $slug
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowBySlug(
        ?string $slug = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($slug)) {
            return null;
        }

        $sqlWhere = sprintf('"articles"."slug" = \'%s\'', $slug);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "articles"."id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateArticleById(
        ?array $row = null,
        ?int $id = null
    ): bool {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            ArticleStore::ARTICLES_TABLE,
            $row,
            $id
        );
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteArticleById(
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

            return $this->updateArticleById($row, $id);
        }

        $this->deleteArticle2TagRelationsByArticleId($id);

        return $this->deleteRowById(ArticleStore::ARTICLES_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreArticleById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => null,
            'is_active' => true
        ];

        return $this->updateArticleById($row, $id);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowsByPage(
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
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $limit * ($page - 1);

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            ORDER BY "articles"."cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
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
    final public function getArticleRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("articles"."id") AS "count"
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $topicId
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowsByTopicId(
        ?int $topicId = null,
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($topicId)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"articles"."topic_id" = \'%d\'',
            $topicId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $limit * ($page - 1);

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            ORDER BY "articles"."cdate"
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere,
            $limit,
            $offset
        );

        return $this->getRow($sql);
    }

    /**
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getArticleRowsCountByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        if (empty($topicId)) {
            return 0;
        }

        $sqlWhere = sprintf(
            '"articles"."topic_id" = \'%d\'',
            $topicId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("articles"."id") AS "count"
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $tagId
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowsByTagId(
        ?int $tagId = null,
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($tagId)) {
            return null;
        }

        $sqlWhere = sprintf('"tags"."id" = \'%d\'', $tagId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("tags"."ddate" IS NULL OR "tags"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "tags"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $limit * ($page - 1);

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "article2tag"
                ON "article2tag"."article_id" = "articles"."id"
            LEFT JOIN "%s" AS "tags" ON "tags"."id" = "article2tag"."tag_id"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            ORDER BY "articles"."cdate" DESC 
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TAG_TO_ARTICLE_TABLE,
            ArticleStore::TAGS_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere,
            $limit,
            $offset
        );

        return $this->getRow($sql);
    }

    /**
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getArticleRowsCountByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        if (empty($tagId)) {
            return 0;
        }

        $sqlWhere = sprintf('"tags"."id" = \'%d\'', $tagId);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("tags"."ddate" IS NULL OR "tags"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "tags"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("articles"."id") AS "count"
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "article2tag"
                ON "article2tag"."article_id" = "articles"."id"
            LEFT JOIN "%s" AS "tags" ON "tags"."id" = "article2tag"."tag_id"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TAG_TO_ARTICLE_TABLE,
            ArticleStore::TAGS_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param int|null $userId
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getArticleRowsByUserId(
        ?int $userId = null,
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($userId)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"articles"."user_id" = \'%d\'',
            $userId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $offset = $limit * ($page - 1);

        $sql = '
            SELECT "articles".*
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s
            ORDER BY "articles"."cdate"
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere,
            $limit,
            $offset
        );

        return $this->getRow($sql);
    }

    /**
     * @param int|null $userId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getArticleRowsCountByUserId(
        ?int $userId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        if (empty($userId)) {
            return 0;
        }

        $sqlWhere = sprintf(
            '"articles"."user_id" = \'%d\'',
            $userId
        );

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '
                %s AND
                ("articles"."ddate" IS NULL OR "articles"."ddate" < 1) AND
                ("topics"."ddate" IS NULL OR "topics"."ddate" < 1) AND
                ("users"."ddate" IS NULL OR "users"."ddate" < 1)
                ',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf(
                '
                %s AND
                "articles"."is_active" = true AND
                "topics"."is_active" = true AND
                "users"."is_active" = true
                ',
                $sqlWhere
            );
        }

        $sql = '
            SELECT COUNT("articles"."id") AS "count"
            FROM "%s" AS "articles"
            LEFT JOIN "%s" AS "topics" ON "topics"."id" = "articles"."topic_id"
            LEFT JOIN "%s" AS "users" ON "users"."id" = "articles"."user_id"
            WHERE %s;
        ';

        $sql = sprintf(
            $sql,
            ArticleStore::ARTICLES_TABLE,
            ArticleStore::TOPICS_TABLE,
            ArticleStore::USERS_TABLE,
            $sqlWhere
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @param IArticleValuesObject|null $articleVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateArticle(
        ?IArticleValuesObject $articleVO = null
    ): bool {
        $id = $articleVO->getId();

        if (empty($id)) {
            $articleVO->setCdate();

            return $this->insertArticle($articleVO->exportRow());
        }

        $articleVO->setMdate();

        return $this->updateArticleById($articleVO->exportRow(), $id);
    }

    /**
     * @param array|null $row
     * @return bool
     */
    final public function insertArticle(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(ArticleStore::ARTICLES_TABLE, $row);
    }

    /**
     * @param int|null $tagId
     * @param int|null $articleId
     * @return bool
     */
    final public function insertArticle2TagRelation(
        ?int $tagId = null,
        ?int $articleId = null
    ): bool {
        if (empty($articleId) || empty($tagId)) {
            return false;
        }

        $row = [
            'article_id' => $articleId,
            'tag_id' => $tagId,
        ];

        return $this->addRow(ArticleStore::TAG_TO_ARTICLE_TABLE, $row);
    }

    /**
     * @param int|null $articleId
     * @return bool
     */
    final public function deleteArticle2TagRelationsByArticleId(
        ?int $articleId = null
    ): bool {
        if (empty($articleId)) {
            return false;
        }

        $condition = sprintf('article_id = %d', $articleId);

        return $this->deleteRows(
            ArticleStore::TAG_TO_ARTICLE_TABLE,
            $condition
        );
    }
}
