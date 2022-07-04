<?php

namespace Sonder\Models\PossibleUser;

use Sonder\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Exceptions\ApiException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\PossibleUser\Forms\PossibleUserForm;
use Sonder\Models\PossibleUser\Interfaces\IPossibleUserApi;
use Sonder\Models\PossibleUser\Interfaces\IPossibleUserModel;

/**
 * @property IPossibleUserModel $model
 */
#[IModelApi]
#[IPossibleUserApi]
final class PossibleUserApi extends ModelApiCore implements IPossibleUserApi
{
    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionCreate(): IResponseObject
    {
        $apiValues = $this->request->getApiValues();

        /* @var $possibleUserForm PossibleUserForm|null */
        $possibleUserForm = $this->model->getForm($apiValues);

        $this->model->create($possibleUserForm);

        if ($possibleUserForm->getStatus()) {
            return $this->getApiResponse();
        }

        throw new ApiException('Can Not Create New Possible User');
    }

    /**
     * @return IResponseObject
     */
    final public function actionGet(): IResponseObject
    {
        $possibleUserVO = $this->model->getVOFromSession();

        if (empty($possibleUserVO)) {
            return $this->getApiResponse(null, false);
        }

        return $this->getApiResponse($possibleUserVO->exportRow());
    }

    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionUpdate(): IResponseObject
    {
        $apiValues = $this->request->getApiValues();

        /* @var $possibleUserForm PossibleUserForm|null */
        $possibleUserForm = $this->model->getForm($apiValues);

        $this->model->update($possibleUserForm);

        if ($possibleUserForm->getStatus()) {
            return $this->getApiResponse();
        }

        throw new ApiException('Can Not Create New Possible User');
    }

    /**
     * @return IResponseObject
     */
    final public function actionDelete(): IResponseObject
    {
        return $this->getApiResponse(
            null,
            $this->model->remove()
        );
    }
}
