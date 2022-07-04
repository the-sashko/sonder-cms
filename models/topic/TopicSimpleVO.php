<?php

namespace Sonder\Models\Topic;

use Exception;
use Sonder\Core\ModelSimpleValuesObject;

final class TopicSimpleValuesObject extends ModelSimpleValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $linkPattern = '/topic/%s/';

    /**
     * @return string|null
     * @throws Exception
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
            'link' => empty($this->getSlug()) ? null : $this->getLink()
        ];
    }
}