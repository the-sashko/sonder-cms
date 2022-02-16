<?php

namespace Sonder\Models\Tag;

use Exception;
use Sonder\Core\ModelValuesObject;

final class TagValuesObject extends ModelValuesObject
{
    const TAGS_LINK = '/tags/';

    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/taxonomy/tag/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/taxonomy/tags/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/taxonomy/tags/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/taxonomy/tags/view/%d/';

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
     * @return string
     * @throws Exception
     */
    final public function getAdminViewLink(): string
    {
        return sprintf($this->adminViewLinkPattern, $this->getId());
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
}
