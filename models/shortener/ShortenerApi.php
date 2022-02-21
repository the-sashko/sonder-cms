<?php

namespace Sonder\Models\Shortener;

use Exception;
use Sonder\Core\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Models\Shortener;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

/**
 * @property Shortener $model
 */
final class ShortenerApi extends ModelApiCore implements IModelApi
{
    /**
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function actionCreate(): ResponseObject
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

        throw new ApiException('Can Not Create New Short Link');
    }

    /**
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function actionGet(): ResponseObject
    {
        $id = $this->request->getApiValue('id');
        $code = $this->request->getApiValue('code');

        if (empty($id) && empty($code)) {
            throw new ApiException(
                'Input Values Are Not Set Or Have Invalid Format'
            );
        }

        if (!empty($id) && !empty($code)) {
            throw new ApiException(
                'Both Input Values "id" And "code" Can Not Be Set'
            );
        }

        if (!empty($code)) {
            return $this->_getRowByCode($code);
        }

        return $this->_getRowById((int)$id);
    }

    /**
     * @return ResponseObject
     * @throws ApiException
     */
    final public function actionUpdate(): ResponseObject
    {
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }

    /**
     * @return ResponseObject
     * @throws ApiException
     */
    final public function actionDelete(): ResponseObject
    {
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }

    /**
     * @param string $code
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getRowByCode(string $code): ResponseObject
    {
        $shortenerVO = $this->model->getVOByCode(
            $code,
            true,
            true
        );

        if (empty($shortenerVO)) {
            throw new ApiException(sprintf(
                'Short Link With Code "%s" Not Exists',
                $code
            ));
        }

        return $this->getApiResponse($shortenerVO->exportRow());
    }

    /**
     * @param int $id
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getRowById(int $id): ResponseObject
    {
        if ($id < 1) {
            throw new ApiException(
                'ID Value Has Invalid Format'
            );
        }

        $shortenerVO = $this->model->getVOById(
            $id,
            true,
            true
        );

        if (empty($shortenerVO)) {
            throw new ApiException(sprintf(
                'Short Link With ID "%d" Not Exists',
                $id
            ));
        }

        return $this->getApiResponse($shortenerVO->exportRow());
    }
}
