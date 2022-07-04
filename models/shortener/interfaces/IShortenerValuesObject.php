<?php

namespace Sonder\Models\Shortener\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IShortenerValuesObject extends IModelValuesObject
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param string|null $code
     * @return void
     */
    public function setCode(?string $code = null): void;

    /**
     * @param string|null $url
     * @return void
     */
    public function setUrl(?string $url = null): void;
}
