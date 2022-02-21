<?php

namespace Sonder\Models\Shortener;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;

final class ShortenerValuesObject extends ModelValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/shortener/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/shortener/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/shortener/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/shortener/view/%d/';

    /**
     * @return string
     * @throws Exception
     */
    final public function getCode(): string
    {
        return (string)$this->get('code');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getUrl(): string
    {
        return (string)$this->get('url');
    }

    /**
     * @param string|null $code
     * @return void
     * @throws Exception
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
     * @throws Exception
     */
    final public function setUrl(?string $url = null): void
    {
        if (!empty($userName)) {
            $this->set('url', $url);
        }
    }
}
