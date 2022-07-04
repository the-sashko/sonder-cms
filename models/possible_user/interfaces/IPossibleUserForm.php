<?php

namespace Sonder\Models\PossibleUser\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IPossibleUserForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @return string|null
     */
    public function getSessionToken(): ?string;

    /**
     * @return array|null
     */
    public function getAdditionalInfo(): ?array;
}
