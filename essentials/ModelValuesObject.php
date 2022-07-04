<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\ModelValuesObject as CoreModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;

abstract class ModelValuesObject extends CoreModelValuesObject
{
    protected const ADMIN_VIEW_LINK_PATTERN = null;

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getAdminViewLink(): ?string
    {
        if (empty(static::ADMIN_VIEW_LINK_PATTERN) || empty($this->getId())) {
            return null;
        }

        return sprintf(static::ADMIN_VIEW_LINK_PATTERN, $this->getId());
    }
}
