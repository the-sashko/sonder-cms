<?php

namespace Sonder\Models\Hit\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Hit\Interfaces\IHitAggregationValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[IHitAggregationValuesObject]
final class HitAggregationValuesObject
    extends BaseModelValuesObject
    implements IHitAggregationValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/hits/aggregation/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/hits/aggregation/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/hits/aggregation/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/hits/aggregation/view/%d/';

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getArticleId(): ?int
    {
        if (!$this->has('article_id')) {
            return null;
        }

        $articleId = $this->get('article_id');

        return empty($articleId) ? null : (int)$articleId;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getCount(): int
    {
        return (int)$this->get('count');
    }

    /**
     * @param int|null $articleId
     * @return void
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setCount(?int $count = null): void
    {
        if (!empty($count)) {
            $this->set('count', $count);
        }
    }
}
