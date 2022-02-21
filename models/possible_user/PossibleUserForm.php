<?php

namespace Sonder\Models\PossibleUser;

use Exception;
use Sonder\Core\ModelFormObject;

final class PossibleUserForm extends ModelFormObject
{
    const SESSION_TOKEN_IS_EMPTY_ERROR_MESSAGE = 'Session token is empty';

    const INVALID_SESSION_TOKEN_ERROR_MESSAGE = 'Invalid session token';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();
    }

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

        if (empty($userId)) {
            return null;
        }

        return (int)$userId;
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

        if (empty($sessionToken)) {
            return null;
        }

        return (string)$sessionToken;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    final public function getAdditionalInfo(): ?array
    {
        if (!$this->has('additional_info')) {
            return null;
        }

        $additionalInfo = $this->get('additional_info');

        if (empty($additionalInfo)) {
            return null;
        }

        return (array)$additionalInfo;
    }
}
