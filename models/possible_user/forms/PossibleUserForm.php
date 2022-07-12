<?php

namespace Sonder\Models\PossibleUser\Forms;

use Sonder\CMS\Essentials\BaseForm;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\PossibleUser\Interfaces\IPossibleUserForm;

#[IModelFormObject]
#[IPossibleUserForm]
final class PossibleUserForm extends BaseForm implements IPossibleUserForm
{
    final public const SESSION_TOKEN_IS_EMPTY_ERROR_MESSAGE = 'Session token is empty';

    final public const INVALID_SESSION_TOKEN_ERROR_MESSAGE = 'Invalid session token';

    /**
     * @return void
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();
    }

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

        if (empty($userId)) {
            return null;
        }

        return (int)$userId;
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

        if (empty($sessionToken)) {
            return null;
        }

        return (string)$sessionToken;
    }

    /**
     * @return array|null
     * @throws ValuesObjectException
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
