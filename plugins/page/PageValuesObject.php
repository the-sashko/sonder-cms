<?php

namespace Sonder\Plugins\Page;

use Sonder\Core\ValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IValuesObject;

#[IValuesObject]
final class PageValuesObject extends ValuesObject
{
    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    public function getTitle(): ?string
    {
        if (!$this->has('title')) {
            return null;
        }

        return $this->get('title');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    public function getContent(): ?string
    {
        if (!$this->has('content')) {
            return null;
        }

        return $this->get('content');
    }

    /**
     * @param string|null $title
     * @return void
     * @throws ValuesObjectException
     */
    public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * @param string|null $content
     * @return void
     * @throws ValuesObjectException
     */
    public function setContent(?string $content = null): void
    {
        $this->set('content', $content);
    }
}
