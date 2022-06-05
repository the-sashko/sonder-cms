<?php

namespace Sonder\Models\Article;

use Exception;
use Sonder\Core\ModelSimpleValuesObject;

final class ArticleSimpleValuesObject extends ModelSimpleValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $linkPattern = '/p/%s/';

    /**
     * @return string|null
     * @throws Exception
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
    final protected function getSlug(): ?string
    {
        if (!$this->has('slug')) {
            return null;
        }

        return (string)$this->get('slug');
    }

    /**
     * @param array|null $params
     * @return array|null
     * @throws Exception
     */
    final public function exportRow(?array $params = null): ?array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'summary' => $this->getSummary(),
            'link' => empty($this->getSlug()) ? null : $this->getLink()
        ];
    }
}
