<?php

namespace Sonder\CMS\Essentials;

use Exception;
use Sonder\Core\ModelValuesObject as CoreModelValuesObject;

abstract class ModelValuesObject extends CoreModelValuesObject
{
    /**
     * @return string|null
     * @throws Exception
     */
    final public function getAdminViewLink(): ?string
    {
        if (empty($this->adminViewLinkPattern)) {
            return null;
        }

        return sprintf($this->adminViewLinkPattern, $this->getId());
    }
}
