<?php

namespace Sonder\Models\Article;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Models\Topic\TopicSimpleValuesObject;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Models\User\UserSimpleValuesObject;
use Sonder\Models\User\UserValuesObject;

final class ArticleValuesObject extends ModelValuesObject
{
    const IMAGE_SIZES = [
        'thumbnail' => [
            'height' => 64,
            'width' => 64,
            'file_prefix' => 'thumb'
        ],

        'list' => [
            'height' => null,
            'width' => 256,
            'file_prefix' => 'l'
        ],

        'single_view' => [
            'height' => null,
            'width' => 512,
            'file_prefix' => 'a'
        ]
    ];

    const IMAGE_FORMAT = 'png';

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
     * @var string
     */
    protected string $adminViewLinkPattern = '/admin/articles/view/%d/';

    /**
     * @var string
     */
    protected string $imageLinkPattern = '/media/articles/%s/%s-%s.png';

    /**
     * @var string
     */
    protected string $missingImageLink = '/assets/img/broken.png';

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
    final public function getImageDir(): ?string
    {
        if (!$this->has('image_dir')) {
            return null;
        }

        return (string)$this->get('image_dir');
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
     * @return TopicSimpleValuesObject|null
     * @throws Exception
     */
    final public function getTopicVO(): ?TopicSimpleValuesObject
    {
        if (!$this->has('topic_simple_vo')) {
            return null;
        }

        return $this->get('topic_simple_vo');
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
     * @return UserSimpleValuesObject|null
     * @throws Exception
     */
    final public function getUserVO(): ?UserSimpleValuesObject
    {
        if (!$this->has('user_simple_vo')) {
            return null;
        }

        return $this->get('user_simple_vo');
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
     * @return array|null
     * @throws Exception
     */
    final public function getTags(): ?array
    {
        if (!$this->has('tags')) {
            return null;
        }

        $tags = $this->get('tags');

        return !empty($tags) && is_array($tags) ? $tags : null;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    final public function getComments(): ?array
    {
        if (!$this->has('comments')) {
            return null;
        }

        $comments = $this->get('comments');

        return !empty($comments) && is_array($comments) ? $comments : null;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    final public function getTagIds(): ?array
    {
        $tags = $this->getTags();

        if (empty($tags)) {
            return null;
        }

        $tagIds = [];

        foreach ($tags as $tag) {
            $tagIds[] = $tag->getId();
        }

        return $tagIds;
    }

    /**
     * @param string $size
     * @return string
     * @throws Exception
     */
    final public function getImageLink(string $size): string
    {
        $imageDir = $this->getImageDir();

        if (empty($imageDir)) {
            return $this->missingImageLink;
        }

        if (!array_key_exists($size, ArticleValuesObject::IMAGE_SIZES)) {
            return $this->missingImageLink;
        }

        $size = ArticleValuesObject::IMAGE_SIZES[$size];

        if (!array_key_exists('file_prefix', $size)) {
            return $this->missingImageLink;
        }

        return sprintf(
            $this->imageLinkPattern,
            $imageDir,
            $this->getSlug(),
            $size['file_prefix']
        );
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function getViewsCount(): int
    {
        if (!$this->has('views_count')) {
            return 0;
        }

        return (int)$this->get('views_count');
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
     * @param string|null $imageDir
     * @return void
     * @throws Exception
     */
    final public function setImageDir(?string $imageDir = null): void
    {
        $this->set('image_dir', $imageDir);
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
     * @param TopicSimpleValuesObject|null $topicVO
     * @return void
     * @throws Exception
     */
    final public function setTopicVO(
        ?TopicSimpleValuesObject $topicVO = null
    ): void
    {
        if (!empty($topicVO)) {
            $this->set('topic_simple_vo', $topicVO);
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
     * @param UserSimpleValuesObject|null $userVO
     * @return void
     * @throws Exception
     */
    final public function setUserVO(
        ?UserSimpleValuesObject $userVO = null
    ): void
    {
        if (!empty($userVO)) {
            $this->set('user_simple_vo', $userVO);
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
     * @param array|null $comments
     * @return void
     * @throws Exception
     */
    final public function setComments(?array $comments = null): void
    {
        if (!empty($comments)) {
            $this->set('comments', $comments);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function setViewsCount(): void
    {
        $viewsCount = $this->getViewsCount();

        $viewsCount++;

        $this->set('views_count', $viewsCount);
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

        if (array_key_exists('topic_simple_vo', $row)) {
            unset($row['topic_simple_vo']);
        }

        if (array_key_exists('user_simple_vo', $row)) {
            unset($row['user_simple_vo']);
        }

        return $row;
    }
}
