<?php

namespace Sonder\Models\Article;

use Sonder\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Article\Interfaces\IArticleApi;
use Sonder\Models\Article\Interfaces\IArticleModel;

/**
 * @property IArticleModel $model
 */
#[IModelApi]
#[IArticleApi]
final class ArticleApi extends ModelApiCore implements IArticleApi
{
    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionCreate(): IResponseObject
    {
        //TODO
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }

    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionGet(): IResponseObject
    {
        //TODO
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }

    /**
     * @return IResponseObject
     * @throws ApiException
     */
    final public function actionUpdate(): IResponseObject
    {
        //TODO
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
        //TODO
        throw new ApiException(
            ApiException::MESSAGE_API_METHOD_IS_NOT_SUPPORTED,
            AppException::CODE_API_METHOD_IS_NOT_SUPPORTED
        );
    }
}
