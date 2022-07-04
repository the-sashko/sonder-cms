<?php

namespace Sonder\Models\Topic\ValuesObjects;

use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicSimpleValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[ITopicValuesObject]
final class TopicValuesObject
    extends ModelValuesObject
    implements ITopicValuesObject
{
    final public const IMAGE_SIZES = [
        'topic' => [
            'height' => 256,
            'width' => 256,
            'file_prefix' => 'topic'
        ]
    ];

    final public const IMAGE_FORMAT = 'png';

    final public const TOPICS_LINK = '/topics/';

    final protected const EDIT_LINK_PATTERN = '/admin/taxonomy/topic/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/taxonomy/topics/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/taxonomy/topics/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/taxonomy/topics/view/%d/';

    private const IMAGE_LINK_PATTERN = '/media/topics/%d-topic.png';

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
     * @return int
     * @throws ValuesObjectException
     */
    final public function getParentId(): int
    {
        return (int)$this->get('parent_id');
    }

    /**
     * @return TopicSimpleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getParentVO(): ?TopicSimpleValuesObject
    {
        if (!$this->has('parent_simple_vo')) {
            return null;
        }

        return $this->get('parent_simple_vo');
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
     * @return string
     * @throws ValuesObjectException
     */
    final public function getImageLink(): string
    {
        return sprintf(
            TopicValuesObject::IMAGE_LINK_PATTERN,
            $this->getId()
        );
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
     * @param int|null $parentId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentId(?int $parentId = null): void
    {
        if (!empty($parentId)) {
            $this->set('parent_id', $parentId);
        }
    }

    /**
     * @param ITopicSimpleValuesObject|null $parentVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentVO(
        ?ITopicSimpleValuesObject $parentVO = null
    ): void {
        if (!empty($parentVO)) {
            $this->set('parent_simple_vo', $parentVO);
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

        if (array_key_exists('parent_simple_vo', $row)) {
            unset($row['parent_simple_vo']);
        }

        return $row;
    }
}
