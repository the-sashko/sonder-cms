<?php

namespace Sonder\Models\Comment\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Models\User\Interfaces\IUserSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICommentSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @return string|null
     */
    public function getUserName(): ?string;

    /**
     * @return string|null
     */
    public function getUserEmail(): ?string;

    /**
     * @return IUserSimpleValuesObject|null
     */
    public function getUserVO(): ?IUserSimpleValuesObject;

    /**
     * @return string|null
     */
    public function getHtml(): ?string;

    /**
     * @param IUserSimpleValuesObject|null $userSimpleVO
     * @return void
     */
    public function setUserVO(
        ?IUserSimpleValuesObject $userSimpleVO = null
    ): void;
}
