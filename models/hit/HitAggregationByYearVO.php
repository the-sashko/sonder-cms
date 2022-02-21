<?php

namespace Sonder\Models\Hit;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;

final class HitAggregationByYearValuesObject extends ModelValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/hits/year/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/hits/year/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/hits/year/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/hits/year/view/%d/';

    /**
     * @return int|null
     * @throws Exception
     */
    public function getArticleId(): ?int
    {
        if (!$this->has('article_id')) {
            return null;
        }

        $articleId = $this->get('article_id');

        return empty($articleId) ? null : (int)$articleId;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getTopicId(): ?int
    {
        if (!$this->has('topic_id')) {
            return null;
        }

        $topicId = $this->get('topic_id');

        return empty($topicId) ? null : (int)$topicId;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getTagId(): ?int
    {
        if (!$this->has('tag_id')) {
            return null;
        }

        $tagId = $this->get('tag_id');

        return empty($tagId) ? null : (int)$tagId;
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function getCount(): int
    {
        return (int)$this->get('count');
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getYear(): ?int
    {
        $year = $this->get('year');

        if (empty($year)) {
            return null;
        }

        return (int)$year;
    }

    /**
     * @param int|null $articleId
     * @return void
     * @throws Exception
     */
    final public function setArticleId(?int $articleId = null): void
    {
        if (!empty($articleId)) {
            $this->set('article_id', $articleId);
        }
    }

    /**
     * @param int|null $topicId
     * @return void
     * @throws Exception
     */
    final public function setTopicId(?int $topicId = null): void
    {
        if (!empty($topicId)) {
            $this->set('topic_id', $topicId);
        }
    }

    /**
     * @param int|null $tagId
     * @return void
     * @throws Exception
     */
    final public function setTagId(?int $tagId = null): void
    {
        if (!empty($tagId)) {
            $this->set('tag_id', $tagId);
        }
    }

    /**
     * @param int|null $count
     * @return void
     * @throws Exception
     */
    final public function setCount(?int $count = null): void
    {
        if (!empty($count)) {
            $this->set('count', $count);
        }
    }

    /**
     * @param int|null $year
     * @return void
     * @throws Exception
     */
    final public function setYear(?int $year = null): void
    {
        if (!empty($year)) {
            $this->set('year', $year);
        }
    }
}
