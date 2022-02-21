<?php

namespace Sonder\Models\PossibleUser;

use Exception;
use Sonder\Core\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\ApiException;
use Sonder\Models\PossibleUser;

/**
 * @property PossibleUser $model
 */
final class PossibleUserApi extends ModelApiCore implements IModelApi
{
    /**
     * @return ResponseObject
     * @throws ApiException
     * @throws Exception
     */
    final public function actionCreate(): ResponseObject
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
     * @return ResponseObject
     * @throws Exception
     */
    final public function actionGet(): ResponseObject
    {
        $possibleUserVO = $this->model->getVOFromSession();

        if (empty($possibleUserVO)) {
            return $this->getApiResponse(null, false);
        }

        return $this->getApiResponse($possibleUserVO->exportRow());
    }

    /**
     * @return ResponseObject
     * @throws ApiException
     * @throws Exception
     */
    final public function actionUpdate(): ResponseObject
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
     * @return ResponseObject
     * @throws Exception
     */
    final public function actionDelete(): ResponseObject
    {
        return $this->getApiResponse(
            null,
            $this->model->removePossibleUser()
        );
    }
}
