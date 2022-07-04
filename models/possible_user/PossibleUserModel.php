<?php

namespace Sonder\Models;

use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\IPossibleUser\Interfaces\IPossibleUserValuesObject;
use Sonder\Models\PossibleUser\Forms\PossibleUserForm;
use Sonder\Models\PossibleUser\Interfaces\IPossibleUserApi;
use Sonder\Models\PossibleUser\Interfaces\IPossibleUserForm;
use Sonder\Models\PossibleUser\Interfaces\IPossibleUserModel;
use Sonder\Models\PossibleUser\ValuesObjects\PossibleUserValuesObject;
use Sonder\Models\Shortener\Interfaces\IShortenerModel;
use Sonder\Models\User\ValuesObjects\UserValuesObject;

/**
 * @property IPossibleUserApi $api
 * @property null $store
 */
#[IModel]
#[IShortenerModel]
final class PossibleUserModel extends BaseModel implements IPossibleUserModel
{
    final protected const ITEMS_ON_PAGE = 10;

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
    final public function remove(): bool
    {
        //TODO

        return false;
    }

    /**
     * @param IPossibleUserForm $possibleUserForm
     * @return bool
     */
    final public function create(IPossibleUserForm $possibleUserForm): bool
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
     * @param IPossibleUserForm $possibleUserForm
     * @return bool
     * @throws ValuesObjectException
     */
    final public function update(IPossibleUserForm $possibleUserForm): bool
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
     * @return IPossibleUserValuesObject
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getVO(
        ?array $row = null
    ): IPossibleUserValuesObject
    {
        /* @var $possibleUserVO PossibleUserValuesObject */
        $possibleUserVO = parent::getVO($row);

        $this->_setUserVOToVO($possibleUserVO);

        return $possibleUserVO;
    }

    /**
     * @param PossibleUserValuesObject $possibleUserVO
     * @return void
     * @throws ValuesObjectException
     * @throws CoreException
     * @throws ModelException
     */
    private function _setUserVOToVO(
        PossibleUserValuesObject $possibleUserVO
    ): void {
        /* @var $userModel UserModel */
        $userModel = $this->getModel('user');

        /* @var $userVO UserValuesObject */
        $userVO = $userModel->getVOById($possibleUserVO->getUserId());

        if (!empty($userVO)) {
            $possibleUserVO->setUserVO($userVO);
        }
    }

    /**
     * @param IPossibleUserForm $possibleUserForm
     * @return void
     * @throws ValuesObjectException
     */
    private function _checkSessionTokenInPossibleUserForm(
        IPossibleUserForm $possibleUserForm
    ): void {
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
