<?php

namespace Sonder\Models\Topic\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelSimpleValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[ITopicSimpleValuesObject]
final class TopicSimpleValuesObject
    extends BaseModelSimpleValuesObject
    implements ITopicSimpleValuesObject
{
    final protected const LINK_PATTERN = '/topic/%s/';

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getTitle(): ?string
    {
        if (!$this->has('title')) {
            return null;
        }

        return (string)$this->get('slug');
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
