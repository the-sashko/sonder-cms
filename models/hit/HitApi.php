<?php

namespace Sonder\Models\Hit;

use Exception;
use Sonder\Core\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Models\Hit;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

/**
 * @property Hit $model
 */
final class HitApi extends ModelApiCore implements IModelApi
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

        /* @var $hitForm HitForm|null */
        $hitForm = $this->model->getForm($apiValues);

        $this->model->saveHit($hitForm);

        if ($hitForm->getStatus()) {
            return $this->getApiResponse();
        }

        throw new ApiException('Can Not Create New Hit');
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
        $type = $this->request->getApiValue('type');

        if (empty($id) || empty($type)) {
            throw new ApiException(
                'Input Values Are Not Set Or Have Invalid Format'
            );
        }

        $response = null;

        switch ($type) {
            case 'article':
                $response = $this->_getHitCountByArticleId((int)$id);
                break;
            case 'topic':
                $response = $this->_getHitCountByTopicId((int)$id);
                break;
            case 'tag':
                $response = $this->_getHitCountByTagId((int)$id);
                break;
        }

        if (empty($response)) {
            throw new ApiException('Input Type Of Hit');
        }

        return $response;
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
     * @param int $id
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getHitCountByArticleId(int $id): ResponseObject
    {
        if ($id < 1) {
            throw new ApiException(
                'ID Value Has Invalid Format'
            );
        }

        $count = $this->model->getCountByArticleId($id, true, true);

        return $this->getApiResponse([
            'count' => $count
        ]);
    }

    /**
     * @param int $id
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getHitCountByTopicId(int $id): ResponseObject
    {
        if ($id < 1) {
            throw new ApiException(
                'ID Value Has Invalid Format'
            );
        }

        $count = $this->model->getCountByTopicId($id, true, true);

        return $this->getApiResponse([
            'count' => $count
        ]);
    }

    /**
     * @param int $id
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getHitCountByTagId(int $id): ResponseObject
    {
        if ($id < 1) {
            throw new ApiException(
                'ID Value Has Invalid Format'
            );
        }

        $count = $this->model->getCountByTagId($id, true, true);

        return $this->getApiResponse([
            'count' => $count
        ]);
    }
}
