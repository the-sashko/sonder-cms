<?php

namespace Sonder\Models\Shortener\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Shortener\Interfaces\IShortenerValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[IShortenerValuesObject]
final class ShortenerValuesObject
    extends BaseModelValuesObject
    implements IShortenerValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/shortener/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/shortener/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/shortener/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/shortener/view/%d/';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getCode(): string
    {
        return (string)$this->get('code');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getUrl(): string
    {
        return (string)$this->get('url');
    }

    /**
     * @param string|null $code
     * @return void
     * @throws ValuesObjectException
     */
    final public function setCode(?string $code = null): void
    {
        if (!empty($code)) {
            $this->set('code', $code);
        }
    }

    /**
     * @param string|null $url
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUrl(?string $url = null): void
    {
        if (!empty($userName)) {
            $this->set('url', $url);
        }
    }
}
