<?php

namespace Sonder\Models\Topic;

use Sonder\Interfaces\IModelApi;
use Sonder\Core\ModelApiCore;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Topic\Interfaces\ITopicApi;
use Sonder\Models\Topic\Interfaces\ITopicModel;

/**
 * @property ITopicModel $model
 */
#[IModelApi]
#[ITopicApi]
final class TopicApi extends ModelApiCore implements ITopicApi
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
