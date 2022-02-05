<?php

namespace Sonder\Models\Topic;

use Exception;
use Sonder\Core\ModelValuesObject;

final class TopicValuesObject extends ModelValuesObject
{
    const IMAGE_SIZES = [
        'topic' => [
            'height' => 256,
            'width' => 256,
            'file_prefix' => 'topic'
        ]
    ];

    const IMAGE_FORMAT = 'png';

    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/taxonomy/topic/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/taxonomy/topics/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/taxonomy/topics/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/taxonomy/topics/view/%d/';

    /**
     * @var string|null
     */
    protected ?string $imageLinkPattern = '/media/topics/%s-topic.png';

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
     * @return int
     * @throws Exception
     */
    final public function getParentId(): int
    {
        return (int)$this->get('parent_id');
    }

    /**
     * @return TopicValuesObject|null
     * @throws Exception
     */
    final public function getParentVO(): ?TopicValuesObject
    {
        if (!$this->has('parent_vo')) {
            return null;
        }

        return $this->get('parent_vo');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getAdminViewLink(): string
    {
        return sprintf($this->adminViewLinkPattern, $this->getId());
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
     * @param int|null $parentId
     * @return void
     * @throws Exception
     */
    final public function setParentId(?int $parentId = null): void
    {
        if (!empty($parentId)) {
            $this->set('parent_id', $parentId);
        }
    }

    /**
     * @param TopicValuesObject|null $parentVO
     * @return void
     * @throws Exception
     */
    final public function setParentVO(?TopicValuesObject $parentVO = null): void
    {
        if (!empty($parentVO)) {
            $this->set('parent_vo', $parentVO);
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

        if (array_key_exists('parent_vo', $row)) {
            unset($row['parent_vo']);
        }

        return $row;
    }
}
