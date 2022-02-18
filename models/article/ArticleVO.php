<?php

namespace Sonder\Models\Article;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Models\User\UserValuesObject;

final class ArticleValuesObject extends ModelValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/article/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/article/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/article/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/articles/view/%d/';

    /**
     * @var string|null
     */
    protected ?string $imageLinkPattern = '/images/articles/%s/%s.png';

    /**
     * @return string
     * @throws Exception
     */
    final public function getTitle(): string
    {
        return (string)$this->get('title');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getSlug(): ?string
    {
        if (!$this->has('slug')) {
            return null;
        }

        return (string)$this->get('slug');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getImage(): ?string
    {
        if (!$this->has('image')) {
            return null;
        }

        return (string)$this->get('image');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getSummary(): ?string
    {
        if (!$this->has('summary')) {
            return null;
        }

        return (string)$this->get('summary');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getText(): ?string
    {
        if (!$this->has('text')) {
            return null;
        }

        return (string)$this->get('text');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getHtml(): ?string
    {
        if (!$this->has('html')) {
            return null;
        }

        return (string)$this->get('html');
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function getTopicId(): int
    {
        return (int)$this->get('topic_id');
    }

    /**
     * @return TopicValuesObject|null
     * @throws Exception
     */
    final public function getTopicVO(): ?TopicValuesObject
    {
        if (!$this->has('topic_vo')) {
            return null;
        }

        return $this->get('topic_vo');
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function getUserId(): int
    {
        return (int)$this->get('user_id');
    }

    /**
     * @return UserValuesObject|null
     * @throws Exception
     */
    final public function getUserVO(): ?UserValuesObject
    {
        if (!$this->has('user_vo')) {
            return null;
        }

        return $this->get('user_vo');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getMetaTitle(): ?string
    {
        if (!$this->has('meta_title')) {
            return null;
        }

        return $this->get('meta_title');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getMetaDescription(): ?string
    {
        if (!$this->has('meta_description')) {
            return null;
        }

        return $this->get('meta_description');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getTags(): ?string
    {
        if (!$this->has('tags')) {
            return null;
        }

        $tags = $this->get('tags');

        return !empty($tags) && is_array($tags) ? $tags : null;
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getImageLink(): string
    {
        return sprintf($this->imageLinkPattern, $this->getSlug());
    }

    /**
     * @param string|null $title
     * @return void
     * @throws Exception
     */
    final public function setTitle(?string $title = null): void
    {
        if (!empty($title)) {
            $this->set('title', $title);
        }
    }

    /**
     * @param string|null $slug
     * @return void
     * @throws Exception
     */
    final public function setSlug(?string $slug = null): void
    {
        if (!empty($slug)) {
            $this->set('slug', $slug);
        }
    }

    /**
     * @param string|null $image
     * @return void
     * @throws Exception
     */
    final public function setImage(?string $image = null): void
    {
        if (!empty($image)) {
            $this->set('image', $image);
        }
    }

    /**
     * @param string|null $summary
     * @return void
     * @throws Exception
     */
    final public function setSummary(?string $summary = null): void
    {
        if (!empty($summary)) {
            $this->set('summary', $summary);
        }
    }

    /**
     * @param string|null $text
     * @return void
     * @throws Exception
     */
    final public function setText(?string $text = null): void
    {
        if (!empty($text)) {
            $this->set('text', $text);
        }
    }

    /**
     * @param string|null $html
     * @return void
     * @throws Exception
     */
    final public function setHtml(?string $html = null): void
    {
        if (!empty($html)) {
            $this->set('html', $html);
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
     * @param TopicValuesObject|null $topicVO
     * @return void
     * @throws Exception
     */
    final public function setTopicVO(?TopicValuesObject $topicVO = null): void
    {
        if (!empty($topicVO)) {
            $this->set('topic_vo', $topicVO);
        }
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws Exception
     */
    final public function setUserId(?int $userId = null): void
    {
        if (!empty($userId)) {
            $this->set('user_id', $userId);
        }
    }

    /**
     * @param UserValuesObject|null $userVO
     * @return void
     * @throws Exception
     */
    final public function setUserVO(?UserValuesObject $userVO = null): void
    {
        if (!empty($userVO)) {
            $this->set('user_vo', $userVO);
        }
    }

    /**
     * @param string|null $metaTitle
     * @return void
     * @throws Exception
     */
    final public function setMetaTitle(?string $metaTitle = null): void
    {
        if (!empty($metaTitle)) {
            $this->set('meta_title', $metaTitle);
        }
    }

    /**
     * @param string|null $metaDescription
     * @return void
     * @throws Exception
     */
    final public function setMetaDescription(
        ?string $metaDescription = null
    ): void
    {
        if (!empty($metaDescription)) {
            $this->set('meta_description', $metaDescription);
        }
    }

    /**
     * @param array|null $tags
     * @return void
     * @throws Exception
     */
    final public function setTags(?array $tags = null): void
    {
        if (!empty($tags)) {
            $this->set('tags', $tags);
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

        if (array_key_exists('topic_vo', $row)) {
            unset($row['topic_vo']);
        }

        if (array_key_exists('user_vo', $row)) {
            unset($row['user_vo']);
        }

        return $row;
    }
}
