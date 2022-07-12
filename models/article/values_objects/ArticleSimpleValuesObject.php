<?php

namespace Sonder\Models\Article\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelSimpleValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Article\Interfaces\IArticleSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IArticleSimpleValuesObject]
final class ArticleSimpleValuesObject
    extends BaseModelSimpleValuesObject
    implements IArticleSimpleValuesObject
{
    final protected const LINK_PATTERN = '/p/%s/';

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
            'summary' => $this->getSummary(),
            'link' => empty($this->getSlug()) ? null : $this->getLink()
        ];
    }
}
