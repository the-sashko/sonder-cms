<?php

namespace Sonder\Models\PossibleUser\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;
use Sonder\Models\IPossibleUser\Interfaces\IPossibleUserValuesObject;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IPossibleUserModel extends IModel
{
    /**
     * @return IPossibleUserValuesObject|null
     */
    public function getVOFromSession(): ?IPossibleUserValuesObject;

    /**
     * @return bool
     */
    public function remove(): bool;

    /**
     * @param IPossibleUserForm $possibleUserForm
     * @return bool
     */
    public function create(IPossibleUserForm $possibleUserForm): bool;

    /**
     * @param IPossibleUserForm $possibleUserForm
     * @return bool
     */
    public function update(IPossibleUserForm $possibleUserForm): bool;
}
