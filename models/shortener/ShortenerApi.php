<?php

namespace Sonder\Models\Shortener;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Shortener\Exceptions\ShortenerApiException;
use Sonder\Models\Shortener\Exceptions\ShortenerException;
use Sonder\Models\Shortener\Forms\ShortenerForm;
use Sonder\Models\Shortener\Interfaces\IShortenerApi;
use Sonder\Models\Shortener\Interfaces\IShortenerModel;

/**
 * @property IShortenerModel $model
 */
#[IModelApi]
#[IShortenerApi]
final class ShortenerApi extends ModelApiCore implements IShortenerApi
{
    /**
     * @return IResponseObject
     * @throws ShortenerApiException
     * @throws ValuesObjectException
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

        /* @var $shortenerForm ShortenerForm|null */
        $shortenerForm = $this->model->getForm($apiValues);

        $this->model->save($shortenerForm);

        if ($shortenerForm->getStatus()) {
            return $this->getApiResponse([
                'code' => $shortenerForm->getCode()
            ]);
        }

        throw new ShortenerApiException(
            ShortenerApiException::MESSAGE_API_CAN_NOT_CREATE_SHORT_LINK,
            ShortenerException::CODE_API_CAN_NOT_CREATE_SHORT_LINK
        );
    }

    /**
     * @return IResponseObject
     * @throws ShortenerApiException
     */
    final public function actionGet(): IResponseObject
    {
        $id = $this->request->getApiValue('id');
        $code = $this->request->getApiValue('code');

        if (empty($id) && empty($code)) {
            throw new ShortenerApiException(
                ShortenerApiException::MESSAGE_API_INPUT_VALUES_HAVE_INVALID_FORMAT,
                ShortenerException::CODE_API_INPUT_VALUES_HAVE_INVALID_FORMAT
            );
        }

        if (!empty($id) && !empty($code)) {
            throw new ShortenerApiException(
                ShortenerApiException::MESSAGE_API_BOTH_ID_AND_CODE_CAN_NOT_BE_SET,
                ShortenerException::CODE_API_BOTH_ID_AND_CODE_CAN_NOT_BE_SET
            );
        }

        if (!empty($code)) {
            return $this->_getRowByCode($code);
        }

        return $this->_getRowById((int)$id);
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
     * @param string $code
     * @return IResponseObject
     * @throws ShortenerApiException
     */
    private function _getRowByCode(string $code): IResponseObject
    {
        $shortenerVO = $this->model->getVOByCode($code);

        if (empty($shortenerVO)) {
            $errorMessage = sprintf(
                ShortenerApiException::MESSAGE_API_SHORT_LINK_WITH_CODE_NOT_EXISTS,
                $code
            );

            throw new ShortenerApiException(
                $errorMessage,
                ShortenerException::CODE_API_SHORT_LINK_WITH_CODE_NOT_EXISTS
            );
        }

        return $this->getApiResponse($shortenerVO->exportRow());
    }

    /**
     * @param int $id
     * @return IResponseObject
     * @throws ShortenerApiException
     */
    private function _getRowById(int $id): IResponseObject
    {
        if ($id < 1) {
            throw new ShortenerApiException(
                ShortenerApiException::MESSAGE_API_ID_VALUE_HAS_INVALID_FORMAT,
                ShortenerException::CODE_API_ID_VALUE_HAS_INVALID_FORMAT
            );
        }

        $shortenerVO = $this->model->getVOById($id);

        if (empty($shortenerVO)) {
            $errorMessage = sprintf(
                ShortenerApiException::MESSAGE_API_SHORT_LINK_WITH_ID_NOT_EXISTS,
                $id
            );

            throw new ShortenerApiException(
                $errorMessage,
                ShortenerException::CODE_API_SHORT_LINK_WITH_ID_NOT_EXISTS
            );
        }

        return $this->getApiResponse($shortenerVO->exportRow());
    }
}
