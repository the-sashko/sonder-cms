<?php

namespace Sonder\Models\Hit;

use Sonder\CMS\Essentials\BaseModelApi;
use Sonder\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Hit\Enums\HitTypesEnum;
use Sonder\Models\Hit\Exceptions\HitApiException;
use Sonder\Models\Hit\Exceptions\HitException;
use Sonder\Models\Hit\Interfaces\IHitApi;
use Sonder\Models\Hit\Interfaces\IHitModel;

/**
 * @property IHitModel $model
 */
#[IModelApi]
#[IHitApi]
final class HitApi extends BaseModelApi implements IHitApi
{
    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionCreate(): IResponseObject
    {
        $apiValues = $this->request->getApiValues();

        if (
            !empty($apiValues) &&
            !array_key_exists('is_active', $apiValues)
        ) {
            $apiValues['is_active'] = true;
        }

        /* @var $hitForm HitForm */
        $hitForm = $this->model->getForm($apiValues);

        $this->model->saveHit($hitForm);

        if ($hitForm->getStatus()) {
            return $this->getApiResponse();
        }

        throw new ApiException('Can Not Create New Hit');
    }

    /**
     * @return IResponseObject
     * @throws HitApiException
     */
    final public function actionGet(): IResponseObject
    {
        $id = $this->request->getApiValue('id');
        $type = $this->request->getApiValue('type');

        if (empty($id) || empty($type)) {
            throw new HitApiException(
                HitApiException::MESSAGE_API_INPUT_VALUES_HAVE_INVALID_FORMAT,
                HitException::CODE_API_INPUT_VALUES_HAVE_INVALID_FORMAT
            );
        }

        $response = null;

        switch ($type) {
            case HitTypesEnum::ARTICLE->value:
                $response = $this->_getHitCountByArticleId((int)$id);
                break;
            case HitTypesEnum::TOPIC->value:
                $response = $this->_getHitCountByTopicId((int)$id);
                break;
            case HitTypesEnum::TAG->value:
                $response = $this->_getHitCountByTagId((int)$id);
                break;
        }

        if (empty($response)) {
            throw new HitApiException(
                HitApiException::MESSAGE_API_INVALID_TYPE,
                HitException::CODE_API_INVALID_TYPE
            );
        }

        return $response;
    }

    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionUpdate(): IResponseObject
    {
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }

    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionDelete(): IResponseObject
    {
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }

    /**
     * @param int $id
     * @return IResponseObject
     * @throws HitApiException
     */
    private function _getHitCountByArticleId(int $id): IResponseObject
    {
        if ($id < 1) {
            throw new HitApiException(
                HitApiException::MESSAGE_API_ID_HAS_INVALID_FORMAT,
                HitException::CODE_API_ID_HAS_INVALID_FORMAT
            );
        }

        $count = $this->model->getCountByArticleId($id);

        return $this->getApiResponse([
            'count' => $count
        ]);
    }

    /**
     * @param int $id
     * @return IResponseObject
     * @throws HitApiException
     */
    private function _getHitCountByTopicId(int $id): IResponseObject
    {
        if ($id < 1) {
            throw new HitApiException(
                HitApiException::MESSAGE_API_ID_HAS_INVALID_FORMAT,
                HitException::CODE_API_ID_HAS_INVALID_FORMAT
            );
        }

        $count = $this->model->getCountByTopicId($id);

        return $this->getApiResponse([
            'count' => $count
        ]);
    }

    /**
     * @param int $id
     * @return IResponseObject
     * @throws HitApiException
     */
    private function _getHitCountByTagId(int $id): IResponseObject
    {
        if ($id < 1) {
            throw new HitApiException(
                HitApiException::MESSAGE_API_ID_HAS_INVALID_FORMAT,
                HitException::CODE_API_ID_HAS_INVALID_FORMAT
            );
        }

        $count = $this->model->getCountByTagId($id);

        return $this->getApiResponse([
            'count' => $count
        ]);
    }
}
