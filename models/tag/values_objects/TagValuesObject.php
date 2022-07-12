<?php

namespace Sonder\Models\Tag\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Tag\Interfaces\ITagValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[ITagValuesObject]
final class TagValuesObject
    extends BaseModelValuesObject
    implements ITagValuesObject
{
    final public const TAGS_LINK = '/tags/';

    final protected const EDIT_LINK_PATTERN = '/admin/taxonomy/tag/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/taxonomy/tags/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/taxonomy/tags/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/taxonomy/tags/view/%d/';

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
     * @return void
     * @throws ValuesObjectException
     */
    final public function setViewsCount(): void
    {
        $viewsCount = $this->getViewsCount();

        $viewsCount++;

        $this->set('views_count', $viewsCount);
    }
}
