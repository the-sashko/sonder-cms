<?php

namespace Sonder\Models\PossibleUser;

use Exception;
use Sonder\Core\ValuesObject;
use Sonder\Models\User\UserValuesObject;

final class PossibleUserValuesObject extends ValuesObject
{
    /**
     * @return int|null
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @return ValuesObject|null
     * @throws Exception
     */
    final public function getAdditionalInfo(): ?ValuesObject
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
     * @return UserValuesObject|null
     * @throws Exception
     */
    final public function getUserVO(): ?UserValuesObject
    {
        if (!$this->has('user_vo')) {
            return null;
        }

        return $this->get('user_vo');
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function setAdditionalInfo(?array $additionalInfo = null): void
    {
        if (!empty($additionalInfo)) {
            $this->set('additional_info', $additionalInfo);
        }
    }

    /**
     * @param UserValuesObject|null $userVO
     * @return void
     * @throws Exception
     */
    final public function setUserVO(?UserValuesObject $userVO = null): void
    {
        if (!empty($userVO)) {
            $this->set('user_vo', $userVO);
        }
    }

    /**
     * @param array|null $params
     * @return array|null
     */
    final public function exportRow(?array $params = null): ?array
    {
        $row = parent::exportRow($params);

        if (empty($row)) {
            return null;
        }

        if (array_key_exists('user_vo', $row) && empty($row['user_vo'])) {
            unset($row['user_vo']);
        }

        if (array_key_exists('ip', $row)) {
            unset($row['ip']);
        }

        return $row;
    }
}
