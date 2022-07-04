<?php

namespace Sonder\Models;

use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Hit\Enums\HitTypesEnum;
use Sonder\Models\Hit\HitForm;
use Sonder\Models\Hit\Interfaces\IHitAggregationValuesObject;
use Sonder\Models\Hit\Interfaces\IHitApi;
use Sonder\Models\Hit\Interfaces\IHitForm;
use Sonder\Models\Hit\ValuesObjects\HitAggregationValuesObject;
use Sonder\Models\Hit\ValuesObjects\HitValuesObject;
use Sonder\Models\Hit\Interfaces\IHitModel;
use Sonder\Models\Hit\Interfaces\IHitStore;
use Sonder\Models\Hit\Interfaces\IHitValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Throwable;

/**
 * @property IHitApi $api
 * @property IHitStore $store
 */
#[IModel]
#[IHitModel]
final class HitModel extends BaseModel implements IHitModel
{
    final protected const ITEMS_ON_PAGE = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IHitValuesObject|null
     * @throws ModelException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IHitValuesObject {
        $row = $this->store->getHitRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $hitVO HitValuesObject */
            $hitVO = $this->getVO($row);

            return $hitVO;
        }

        return null;
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IHitAggregationValuesObject|null
     */
    final public function getAggregationVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IHitAggregationValuesObject {
        $row = $this->store->getAggregationRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->_getAggregationVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws ModelException
     */
    final public function getHitVOsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getHitRowsByPage(
            $page,
            HitModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getAggregationsVOsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getAggregationRowsByPage(
            $page,
            HitModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return array_map(function (array $row) {
            return $this->_getAggregationVO($row);
        }, $rows);
    }

    final public function getHitsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getHitsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / HitModel::ITEMS_ON_PAGE);

        if ($pageCount * HitModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    final public function getAggregationsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getHitAggregationsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / HitModel::ITEMS_ON_PAGE);

        if ($pageCount * HitModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        return $this->store->getCountByArticleId(
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );
    }

    /**
     * @param int|null $topicId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCountByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        return $this->store->getCountByTopicId(
            $topicId,
            $excludeRemoved,
            $excludeInactive
        );
    }

    /**
     * @param int|null $tagId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCountByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        return $this->store->getCountByTagId(
            $tagId,
            $excludeRemoved,
            $excludeInactive
        );
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeHitById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteHitById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeAggregationById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteAggregationById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreHitById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreHitById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreAggregationById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreAggregationById($id);
    }

    /**
     * @param IHitForm $hitForm
     * @return bool
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function saveHit(IHitForm $hitForm): bool
    {
        $hitForm->checkInputValues();

        if (!$hitForm->getStatus()) {
            return false;
        }

        $this->_checkHitIdInHitForm($hitForm);

        $type = $hitForm->getType();

        switch ($type) {
            case HitTypesEnum::ARTICLE->value:
                $this->_checkArticleIdInHitForm($hitForm);
                break;
            case HitTypesEnum::TOPIC->value:
                $this->_checkTopicIdInHitForm($hitForm);
                break;
            case HitTypesEnum::TAG->value:
                $this->_checkTagIdInHitForm($hitForm);
                break;
            default:
                $hitForm->setStatusFail();

                $hitForm->setError(
                    sprintf(
                        HitForm::INVALID_TYPE_ERROR_MESSAGE,
                        $type
                    )
                );
        }

        if (!$hitForm->getStatus()) {
            return false;
        }

        $hitVO = $this->_getHitVOFromHitForm($hitForm, true);

        try {
            if (!$this->store->insertOrUpdateHit($hitVO)) {
                $hitForm->setStatusFail();

                return false;
            }
        } catch (Throwable $thr) {
            $hitForm->setStatusFail();
            $hitForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param IHitForm $hitForm
     * @return bool
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function saveAggregation(IHitForm $hitForm): bool
    {
        $hitForm->checkInputValues();

        if (!$hitForm->getStatus()) {
            return false;
        }

        $this->_checkAggregationIdInHitForm($hitForm);

        $type = $hitForm->getType();

        switch ($type) {
            case HitTypesEnum::ARTICLE->value:
                $this->_checkArticleIdInHitForm($hitForm);
                break;
            case HitTypesEnum::TOPIC->value:
                $this->_checkTopicIdInHitForm($hitForm);
                break;
            case HitTypesEnum::TAG->value:
                $this->_checkTagIdInHitForm($hitForm);
                break;
            default:
                $hitForm->setStatusFail();

                $hitForm->setError(
                    sprintf(
                        HitForm::INVALID_AGGREGATION_TYPE_ERROR_MESSAGE,
                        $type
                    )
                );
        }

        if (!$hitForm->getStatus()) {
            return false;
        }

        $aggregationVO = $this->_getAggregationVOFromHitForm(
            $hitForm,
            true
        );

        try {
            if (!$this->store->insertOrUpdateAggregation($aggregationVO)) {
                $hitForm->setStatusFail();

                return false;
            }
        } catch (Throwable $thr) {
            $hitForm->setStatusFail();
            $hitForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param IHitForm $hitForm
     * @return void
     * @throws ValuesObjectException
     */
    private function _checkHitIdInHitForm(IHitForm $hitForm): void
    {
        $id = $hitForm->getId();

        if (empty($id)) {
            return;
        }

        $hitVO = $this->_getHitVOFromHitForm($hitForm);

        if (empty($hitVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(
                sprintf(
                    HitForm::HITS_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param IHitForm $hitForm
     * @return void
     */
    private function _checkAggregationIdInHitForm(IHitForm $hitForm): void
    {
        $id = $hitForm->getId();

        if (empty($id)) {
            return;
        }

        $aggregationVO = $this->_getAggregationVOFromHitForm($hitForm);

        if (empty($aggregationVO)) {
            $hitForm->setStatusFail();

            $errorMessage = sprintf(
                HitForm::HITS_AGGREGATION_NOT_EXISTS_ERROR_MESSAGE,
                $id
            );

            $hitForm->setError($errorMessage);
        }
    }

    /**
     * @param IHitForm $hitForm
     * @return void
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _checkArticleIdInHitForm(IHitForm $hitForm): void
    {
        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        $articleVO = $articleModel->getVOById($hitForm->getArticleId());

        if (empty($articleVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(
                sprintf(
                    HitForm::ARTICLE_NOT_EXISTS_ERROR_MESSAGE,
                    $hitForm->getId()
                )
            );
        }
    }

    /**
     * @param IHitForm $hitForm
     * @return void
     * @throws CoreException
     * @throws ModelException
     */
    private function _checkTopicIdInHitForm(IHitForm $hitForm): void
    {
        /* @var $topicModel TopicModel */
        $topicModel = $this->getModel('topic');

        $topicVO = $topicModel->getVOById($hitForm->getTopicId());

        if (empty($topicVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(
                sprintf(
                    HitForm::TOPIC_NOT_EXISTS_ERROR_MESSAGE,
                    $hitForm->getId()
                )
            );
        }
    }

    /**
     * @param IHitForm $hitForm
     * @return void
     * @throws CoreException
     * @throws ModelException
     */
    private function _checkTagIdInHitForm(IHitForm $hitForm): void
    {
        /* @var $tagModel TagModel */
        $tagModel = $this->getModel('tag');

        $tagVO = $tagModel->getVOById($hitForm->getTagId());

        if (empty($tagVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(
                sprintf(
                    HitForm::TAG_NOT_EXISTS_ERROR_MESSAGE,
                    $hitForm->getId()
                )
            );
        }
    }

    /**
     * @param IHitForm $hitForm
     * @param bool $isCreateVOIfEmptyId
     * @return IHitValuesObject|null
     * @throws ValuesObjectException
     */
    private function _getHitVOFromHitForm(
        IHitForm $hitForm,
        bool $isCreateVOIfEmptyId = false
    ): ?IHitValuesObject {
        $row = null;

        $id = $hitForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getHitRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $hitVO = new HitValuesObject($row);

        $hitVO->setIsActive($hitForm->isActive());

        return $hitVO;
    }

    /**
     * @param IHitForm $hitForm
     * @param bool $isCreateVOIfEmptyId
     * @return IHitAggregationValuesObject|null
     */
    private function _getAggregationVOFromHitForm(
        IHitForm $hitForm,
        bool $isCreateVOIfEmptyId = false
    ): ?IHitAggregationValuesObject {
        $row = null;

        $id = $hitForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getAggregationRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $aggregationVO = $this->_getAggregationVO($row);

        $aggregationVO->setIsActive($hitForm->isActive());

        return $aggregationVO;
    }

    /**
     * @param array|null $row
     * @return IHitAggregationValuesObject|null
     */
    private function _getAggregationVO(
        ?array $row = null
    ): ?IHitAggregationValuesObject {
        return new HitAggregationValuesObject($row);
    }
}
