<?php

namespace Sonder\Models\Article\ValuesObjects;

use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Article\Interfaces\IArticleValuesObject;
use Sonder\Models\User\Interfaces\IUserSimpleValuesObject;
use Sonder\Models\User\ValuesObjects\UserSimpleValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicSimpleValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[IArticleValuesObject]
final class ArticleValuesObject
    extends ModelValuesObject
    implements IArticleValuesObject
{
    final public const IMAGE_SIZES = [
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

    final public const IMAGE_FORMAT = 'png';

    final protected const EDIT_LINK_PATTERN = '/admin/article/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/article/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/article/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/articles/view/%d/';

    private const IMAGE_LINK_PATTERN = '/media/articles/%s/%s-%s.png';

    private const MISSING_IMAGE_LINK = '/assets/img/broken.png';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getTitle(): string
    {
        return (string)$this->get('title');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getTopicId(): int
    {
        return (int)$this->get('topic_id');
    }

    /**
     * @return ITopicSimpleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getTopicVO(): ?ITopicSimpleValuesObject
    {
        if (!$this->has('topic_simple_vo')) {
            return null;
        }

        return $this->get('topic_simple_vo');
    }

    /**
     * @return int
     * @throws ValuesObjectException
     */
    final public function getUserId(): int
    {
        return (int)$this->get('user_id');
    }

    /**
     * @return UserSimpleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getUserVO(): ?IUserSimpleValuesObject
    {
        if (!$this->has('user_simple_vo')) {
            return null;
        }

        return $this->get('user_simple_vo');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getImageLink(string $size): string
    {
        $imageDir = $this->getImageDir();

        if (empty($imageDir)) {
            return ArticleValuesObject::MISSING_IMAGE_LINK;
        }

        if (!array_key_exists($size, ArticleValuesObject::IMAGE_SIZES)) {
            return ArticleValuesObject::MISSING_IMAGE_LINK;
        }

        $size = ArticleValuesObject::IMAGE_SIZES[$size];

        if (!array_key_exists('file_prefix', $size)) {
            return ArticleValuesObject::MISSING_IMAGE_LINK;
        }

        return sprintf(
            ArticleValuesObject::IMAGE_LINK_PATTERN,
            $imageDir,
            $this->getSlug(),
            $size['file_prefix']
        );
    }

    /**
     * @return int
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setImageDir(?string $imageDir = null): void
    {
        $this->set('image_dir', $imageDir);
    }

    /**
     * @param string|null $summary
     * @return void
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setTopicId(?int $topicId = null): void
    {
        if (!empty($topicId)) {
            $this->set('topic_id', $topicId);
        }
    }

    /**
     * @param ITopicSimpleValuesObject|null $topicVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setTopicVO(
        ?ITopicSimpleValuesObject $topicVO = null
    ): void {
        if (!empty($topicVO)) {
            $this->set('topic_simple_vo', $topicVO);
        }
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserId(?int $userId = null): void
    {
        if (!empty($userId)) {
            $this->set('user_id', $userId);
        }
    }

    /**
     * @param IUserSimpleValuesObject|null $userVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserVO(
        ?IUserSimpleValuesObject $userVO = null
    ): void {
        if (!empty($userVO)) {
            $this->set('user_simple_vo', $userVO);
        }
    }

    /**
     * @param string|null $metaTitle
     * @return void
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setMetaDescription(
        ?string $metaDescription = null
    ): void {
        if (!empty($metaDescription)) {
            $this->set('meta_description', $metaDescription);
        }
    }

    /**
     * @param array|null $tags
     * @return void
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setComments(?array $comments = null): void
    {
        if (!empty($comments)) {
            $this->set('comments', $comments);
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function setViewsCount(): void
    {
        $viewsCount = $this->getViewsCount();

        $viewsCount++;

        $this->set('views_count', $viewsCount);
    }

    /**
     * @return array
     */
    final public function exportRow(): array
    {
        $row = parent::exportRow();

        if (empty($row)) {
            return $row;
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
