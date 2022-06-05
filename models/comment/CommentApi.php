<?php

namespace Sonder\Models\Comment;

use Exception;
use Sonder\Core\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Models\Comment;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

/**
 * @property Comment $model
 */
final class CommentApi extends ModelApiCore implements IModelApi
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

        /* @var $commentForm CommentForm|null */
        $commentForm = $this->model->getForm($apiValues);

        $this->model->save($commentForm);

        if ($commentForm->getStatus()) {
            return $this->getApiResponse();
        }

        throw new ApiException('Can Not Create New Comment');
    }

    /**
     * @return ResponseObject
     * @throws ApiException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function actionGet(): ResponseObject
    {
        $sessionToken = $this->request->getApiValue('session_token');
        $userId = $this->request->getApiValue('additional_info');

        if (empty($id) && empty($articleId) && empty($userId)) {
            throw new ApiException(
                'Input Values Are Not Set Or Have Invalid Format'
            );
        }

        if (!empty($id) && !empty($articleId)) {
            throw new ApiException(
                'Both Input Values "id" And "article_id" Can Not Be Set'
            );
        }

        if (!empty($id) && !empty($userId)) {
            throw new ApiException(
                'Both Input Values "id" And "user_id" Can Not Be Set'
            );
        }

        if (!empty($articleId) && !empty($userId)) {
            throw new ApiException(
                'Both Input Values "article_id" And "user_id" Can ' .
                'Not Be Set'
            );
        }

        if (!empty($articleId)) {
            return $this->_getRowsByArticleId((int)$articleId);
        }

        if (!empty($userId)) {
            return $this->_getRowsByUserId((int)$userId);
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

        $commentVO = $this->model->getSimpleVOById(
            $id,
            true,
            true
        );

        if (empty($commentVO)) {
            throw new ApiException(sprintf(
                'Comment With ID "%d" Not Exists',
                $id
            ));
        }

        return $this->getApiResponse($commentVO->exportRow());
    }

    /**
     * @param int $articleId
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getRowsByArticleId(int $articleId): ResponseObject
    {
        $comments = $this->model->getSimpleCommentsByArticleId($articleId);

        if (empty($comments)) {
            return $this->getApiResponse([
                'rows' => []
            ]);
        }

        $comments = array_map(function (CommentValuesObject $commentVO) {
            return $commentVO->exportRow();
        }, $comments);

        return $this->getApiResponse([
            'rows' => $comments
        ]);
    }

    /**
     * @param int $userId
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getRowsByUserId(int $userId): ResponseObject
    {
        $comments = $this->model->getSimpleCommentsByUserId($userId);

        if (empty($comments)) {
            return $this->getApiResponse([
                'rows' => []
            ]);
        }

        $comments = array_map(function (CommentValuesObject $commentVO) {
            return $commentVO->exportRow();
        }, $comments);

        return $this->getApiResponse([
            'rows' => $comments
        ]);
    }
}
