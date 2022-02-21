<?php

namespace Sonder\Models\Hit;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;

final class HitValuesObject extends ModelValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/hit/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/hit/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/hit/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/hit/view/%d/';

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
     * @return string
     * @throws Exception
     */
    final public function getIp(): string
    {
        return (string)$this->get('ip');
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
     * @param string|null $ip
     * @return void
     * @throws Exception
     */
    final public function setIp(?string $ip = null): void
    {
        if (!empty($ip)) {
            $this->set('ip', $ip);
        }
    }

    /**
     * @param array|null $params
     * @return array|null
     */
    final public function exportRow(?array $params = null): ?array
    {
        $row = parent::exportRow($params);

        if (empty($row)) {
            return null;
        }

        if (array_key_exists('ip', $row)) {
            unset($row['ip']);
        }

        return $row;
    }
}
