<?php

namespace Sonder\Models\Tag\ValuesObjects;

use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Tag\Interfaces\ITagSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[ITagSimpleValuesObject]
final class TagSimpleValuesObject
    extends ModelSimpleValuesObject
    implements ITagSimpleValuesObject
{
    final protected const LINK_PATTERN = '/tag/%s/';

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getTitle(): ?string
    {
        if (!$this->has('title')) {
            return null;
        }

        return (string)$this->get('title');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final protected function getSlug(): ?string
    {
        if (!$this->has('slug')) {
            return null;
        }

        return (string)$this->get('slug');
    }

    /**
     * @return array
     * @throws ValuesObjectException
     */
    final public function exportRow(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'link' => empty($this->getSlug()) ? null : $this->getLink()
        ];
    }
}
