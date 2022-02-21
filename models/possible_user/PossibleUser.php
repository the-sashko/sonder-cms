<?php

namespace Sonder\Models;

use Exception;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\PossibleUser\PossibleUserForm;
use Sonder\Models\PossibleUser\PossibleUserValuesObject;
use Sonder\Models\User\UserValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

/**
 * @property null $store
 */
final class PossibleUser extends BaseModel
{
    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @return PossibleUserValuesObject|null
     */
    final public function getVOFromSession(): ?PossibleUserValuesObject
    {
        //TODO

        return null;
    }

    /**
     * @return bool
     */
    final public function removePossibleUser(): bool
    {
        //TODO

        return false;
    }

    /**
     * @param PossibleUserForm $possibleUserForm
     * @return bool
     * @throws Exception
     */
    final public function create(PossibleUserForm $possibleUserForm): bool
    {
        $possibleUserForm->checkInputValues();

        if (!$possibleUserForm->getStatus()) {
            return false;
        }

        $possibleUserVO = $this->_getVOFromPossibleUserForm(
            $possibleUserForm,
            true
        );

        //TODO

        return true;
    }

    /**
     * @param PossibleUserForm $possibleUserForm
     * @return bool
     * @throws Exception
     */
    final public function update(PossibleUserForm $possibleUserForm): bool
    {
        $possibleUserForm->checkInputValues();

        if (!$possibleUserForm->getStatus()) {
            return false;
        }

        $this->_checkSessionTokenInPossibleUserForm($possibleUserForm);

        if (!$possibleUserForm->getStatus()) {
            return false;
        }

        $possibleUserVO = $this->_getVOFromPossibleUserForm(
            $possibleUserForm,
            true
        );

        //TODO

        return true;
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $possibleUserVO PossibleUserValuesObject */
        $possibleUserVO = parent::getVO($row);

        $this->_setUserVOToVO($possibleUserVO);

        return $possibleUserVO;
    }

    /**
     * @param PossibleUserValuesObject $possibleUserVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setUserVOToVO(
        PossibleUserValuesObject $possibleUserVO
    ): void
    {
        /* @var $userModel User */
        $userModel = $this->getModel('user');

        /* @var $userVO UserValuesObject */
        $userVO = $userModel->getVOById($possibleUserVO->getUserId());

        if (!empty($userVO)) {
            $possibleUserVO->setUserVO($userVO);
        }
    }

    /**
     * @param PossibleUserForm $possibleUserForm
     * @return void
     * @throws Exception
     */
    private function _checkSessionTokenInPossibleUserForm(
        PossibleUserForm $possibleUserForm
    ): void
    {
        if (empty($possibleUserForm->getSessionToken())) {
            $possibleUserForm->setStatusFail();

            $possibleUserForm->setError(
                PossibleUserForm::SESSION_TOKEN_IS_EMPTY_ERROR_MESSAGE
            );

            return;
        }

        $possibleUserVO = $this->getVOFromSession();

        if (empty($possibleUserVO)) {
            $possibleUserForm->setStatusFail();

            $possibleUserForm->setError(
                PossibleUserForm::INVALID_SESSION_TOKEN_ERROR_MESSAGE
            );

            return;
        }

        $sessionTokenFromForm = $possibleUserForm->getSessionToken();

        if ($possibleUserVO->getSessionToken() != $sessionTokenFromForm) {
            $possibleUserForm->setStatusFail();

            $possibleUserForm->setError(
                PossibleUserForm::INVALID_SESSION_TOKEN_ERROR_MESSAGE
            );
        }

    }
}
