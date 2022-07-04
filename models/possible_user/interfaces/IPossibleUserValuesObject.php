<?php

namespace Sonder\Models\IPossibleUser\Interfaces;

use Attribute;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\User\Interfaces\IUserValuesObject;

#[IValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IPossibleUserValuesObject extends IValuesObject
{
    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @return string|null
     */
    public function getIp(): ?string;

    /**
     * @return string|null
     */
    public function getSessionToken(): ?string;

    /**
     * @return IValuesObject|null
     */
    public function getAdditionalInfo(): ?IValuesObject;

    /**
     * @return IUserValuesObject|null
     */
    public function getUserVO(): ?IUserValuesObject;

    /**
     * @param int|null $userId
     * @return void
     */
    public function setUserId(?int $userId = null): void;

    /**
     * @param string|null $ip
     * @return void
     */
    public function setIp(?string $ip = null): void;

    /**
     * @param string|null $sessionToken
     * @return void
     */
    public function setSessionToken(?string $sessionToken = null): void;

    /**
     * @param array|null $additionalInfo
     * @return void
     */
    public function setAdditionalInfo(?array $additionalInfo = null): void;

    /**
     * @param IUserValuesObject|null $userVO
     * @return void
     */
    public function setUserVO(?IUserValuesObject $userVO = null): void;
}
