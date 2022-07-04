<?php

namespace Sonder\Models\Comment;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Comment\Exceptions\CommentApiException;
use Sonder\Models\Comment\Exceptions\CommentException;
use Sonder\Models\Comment\Interfaces\ICommentApi;
use Sonder\Models\Comment\Interfaces\ICommentForm;
use Sonder\Models\Comment\Interfaces\ICommentModel;
use Sonder\Models\Comment\Interfaces\ICommentValuesObject;



/**
 * @property ICommentModel $model
 */
#[IModelApi]
#[ICommentApi]
final class CommentApi extends ModelApiCore implements ICommentApi
{
    /**
     * @return IResponseObject
     * @throws CommentApiException
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

        /* @var $commentForm ICommentForm|null */
        $commentForm = $this->model->getForm($apiValues);

        $this->model->save($commentForm);

        if ($commentForm->getStatus()) {
            return $this->getApiResponse();
        }

        throw new CommentApiException(
            CommentApiException::MESSAGE_API_CAN_NOT_CREATE_COMMENT,
            CommentException::CODE_API_CAN_NOT_CREATE_COMMENT
        );
    }

    /**
     * @return IResponseObject
     * @throws CommentApiException
     * @throws ValuesObjectException
     */
    final public function actionGet(): IResponseObject
    {
        $sessionToken = $this->request->getApiValue('session_token');
        $userId = $this->request->getApiValue('additional_info');

        if (empty($id) && empty($articleId) && empty($userId)) {
            throw new CommentApiException(
                CommentApiException::MESSAGE_API_INPUT_VALUES_HAVE_INVALID_FORMAT,
                CommentException::CODE_API_INPUT_VALUES_HAVE_INVALID_FORMAT
            );
        }

        if (!empty($id) && !empty($articleId)) {
            throw new CommentApiException(
                CommentApiException::MESSAGE_API_BOTH_ID_AND_ARTICLE_ID_CAN_NOT_BE_SET,
                CommentException::CODE_API_BOTH_ID_AND_ARTICLE_ID_CAN_NOT_BE_SET
            );
        }

        if (!empty($id) && !empty($userId)) {
            throw new CommentApiException(
                CommentApiException::MESSAGE_API_BOTH_ID_AND_USER_ID_CAN_NOT_BE_SET,
                CommentException::CODE_API_BOTH_ID_AND_USER_ID_CAN_NOT_BE_SET
            );
        }

        if (!empty($articleId) && !empty($userId)) {
            throw new CommentApiException(
                CommentApiException::MESSAGE_API_BOTH_ARTICLE_ID_AND_USER_ID_CAN_NOT_BE_SET,
                CommentException::CODE_API_BOTH_ARTICLE_ID_AND_USER_ID_CAN_NOT_BE_SET
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
     * @throws CommentApiException
     * @throws ValuesObjectException
     */
    private function _getRowById(int $id): IResponseObject
    {
        if ($id < 1) {
            throw new CommentApiException(
                CommentApiException::MESSAGE_API_ID_VALUE_HAS_INVALID_FORMAT,
                CommentException::CODE_API_ID_VALUE_HAS_INVALID_FORMAT
            );
        }

        $commentVO = $this->model->getSimpleVOById($id);

        if (empty($commentVO)) {
            $errorMessage = sprintf(
                CommentApiException::MESSAGE_API_COMMENT_WITH_ID_NOT_EXISTS,
                $id
            );

            throw new CommentApiException(
                $errorMessage,
                CommentException::CODE_API_COMMENT_WITH_ID_NOT_EXISTS
            );
        }

        return $this->getApiResponse($commentVO->exportRow());
    }

    /**
     * @param int $articleId
     * @return IResponseObject
     */
    private function _getRowsByArticleId(int $articleId): IResponseObject
    {
        $comments = $this->model->getCommentsByArticleId($articleId);

        if (empty($comments)) {
            return $this->getApiResponse([
                'rows' => []
            ]);
        }

        $comments = array_map(function (ICommentValuesObject $commentVO) {
            return $commentVO->exportRow();
        }, $comments);

        return $this->getApiResponse([
            'rows' => $comments
        ]);
    }

    /**
     * @param int $userId
     * @return IResponseObject
     */
    private function _getRowsByUserId(int $userId): IResponseObject
    {
        $comments = $this->model->getCommentsByUserId($userId);

        if (empty($comments)) {
            return $this->getApiResponse([
                'rows' => []
            ]);
        }

        $comments = array_map(function (ICommentValuesObject $commentVO) {
            return $commentVO->exportRow();
        }, $comments);

        return $this->getApiResponse([
            'rows' => $comments
        ]);
    }
}
