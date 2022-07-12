<?php

namespace Sonder\Models\PossibleUser\ValuesObjects;

use Sonder\Core\ValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\IPossibleUser\Interfaces\IPossibleUserValuesObject;
use Sonder\Models\User\Interfaces\IUserValuesObject;

#[IValuesObject]
#[IPossibleUserValuesObject]
final class PossibleUserValuesObject
    extends ValuesObject
    implements IPossibleUserValuesObject
{
    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getUserId(): ?int
    {
        if (!$this->has('user_id')) {
            return null;
        }

        $userId = $this->get('user_id');

        return empty($userId) ? null : (int)$userId;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getIp(): ?string
    {
        if (!$this->has('ip')) {
            return null;
        }

        $ip = $this->get('ip');

        return empty($ip) ? null : (string)$ip;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getSessionToken(): ?string
    {
        if (!$this->has('session_token')) {
            return null;
        }

        $sessionToken = $this->get('session_token');

        return empty($sessionToken) ? null : (string)$sessionToken;
    }

    /**
     * @return IValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getAdditionalInfo(): ?IValuesObject
    {
        if (!$this->has('additional_info')) {
            return null;
        }

        $additionalInfo = (string)$this->get('additional_info');
        $additionalInfo = (array)json_decode($additionalInfo, true);

        if (empty($additionalInfo)) {
            return null;
        }

        return new ValuesObject($additionalInfo);
    }

    /**
     * @return IUserValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getUserVO(): ?IUserValuesObject
    {
        if (!$this->has('user_vo')) {
            return null;
        }

        return $this->get('user_vo');
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserId(?int $userId = null): void
    {
        if (!empty($userId)) {
            $this->set('user_id', $userId);
        }
    }

    /**
     * @param string|null $ip
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIp(?string $ip = null): void
    {
        if (!empty($ip)) {
            $this->set('ip', $ip);
        }
    }

    /**
     * @param string|null $sessionToken
     * @return void
     * @throws ValuesObjectException
     */
    final public function setSessionToken(?string $sessionToken = null): void
    {
        if (!empty($sessionToken)) {
            $this->set('session_token', $sessionToken);
        }
    }

    /**
     * @param array|null $additionalInfo
     * @return void
     * @throws ValuesObjectException
     */
    final public function setAdditionalInfo(?array $additionalInfo = null): void
    {
        if (!empty($additionalInfo)) {
            $this->set('additional_info', $additionalInfo);
        }
    }

    /**
     * @param IUserValuesObject|null $userVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserVO(?IUserValuesObject $userVO = null): void
    {
        if (!empty($userVO)) {
            $this->set('user_vo', $userVO);
        }
    }

    /**
     * @return array
     */
    final public function exportRow(): array
    {
        $row = parent::exportRow();

        if (empty($row)) {
            return $row;
        }

        if (array_key_exists('user_vo', $row)) {
            unset($row['user_vo']);
        }

        if (array_key_exists('ip', $row)) {
            unset($row['ip']);
        }

        return $row;
    }
}
