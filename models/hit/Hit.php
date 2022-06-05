<?php

namespace Sonder\Models;

use Exception;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Core\ValuesObject;
use Sonder\Models\Hit\HitAggregationByDayValuesObject;
use Sonder\Models\Hit\HitAggregationByMonthValuesObject;
use Sonder\Models\Hit\HitAggregationByYearValuesObject;
use Sonder\Models\Hit\HitForm;
use Sonder\Models\Hit\HitStore;
use Sonder\Models\Hit\HitValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Throwable;

/**
 * @property HitStore $store
 */
final class Hit extends BaseModel
{
    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ValuesObject
    {
        $row = $this->store->getHitRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param string $type
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ModelValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAggregationVOById(
        string $type,
        ?int   $id = null,
        bool   $excludeRemoved = true,
        bool   $excludeInactive = true
    ): ?ModelValuesObject
    {
        $row = $this->store->getAggregationRowById(
            $type,
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->_getAggregationVO($type, $row);
        }

        return null;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getHitVOsByPage(
        int  $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $rows = $this->store->getHitRowsByPage(
            $page,
            $this->itemsOnPage,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param string $type
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAggregationsVOsByPage(
        string $type,
        int    $page,
        bool   $excludeRemoved = true,
        bool   $excludeInactive = true
    ): ?array
    {
        $rows = $this->store->getAggregationRowsByPage(
            $type,
            $page,
            $this->itemsOnPage,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return array_map(function (array $row) use ($type) {
            return $this->_getAggregationVO($type, $row);
        }, $rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getHitsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        $rowsCount = $this->store->getHitsRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param string $type
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getAggregationsPageCount(
        string $type,
        bool   $excludeRemoved = true,
        bool   $excludeInactive = true
    ): int
    {
        $rowsCount = $this->store->getAggregationRowsCount(
            $type,
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCountByTopicId(
        ?int $topicId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCountByTagId(
        ?int $tagId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        return $this->store->getCountByTagId(
            $tagId,
            $excludeRemoved,
            $excludeInactive
        );
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeHitById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteHitById($id);
    }

    /**
     * @param string $type
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeAggregationById(
        string $type,
        ?int $id = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteAggregationById($type, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreHitById(
        ?int $id = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreHitById($id);
    }

    /**
     * @param string $type
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreAggregationById(
        string $type,
        ?int $id = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreAggregationById($type, $id);
    }

    /**
     * @param HitForm $hitForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function saveHit(HitForm $hitForm): bool
    {
        $hitForm->checkInputValues();

        if (!$hitForm->getStatus()) {
            return false;
        }

        $this->_checkHitIdInHitForm($hitForm);

        switch ($hitForm->getType()) {
            case 'article':
                $this->_checkArticleIdInHitForm($hitForm);
                break;
            case 'type':
                $this->_checkTopicIdInHitForm($hitForm);
                break;
            case 'tag':
                $this->_checkTagIdInHitForm($hitForm);
                break;
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
     * @param HitForm $hitForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function saveAggregation(HitForm $hitForm): bool
    {
        $hitForm->checkInputValues();

        if (!$hitForm->getStatus()) {
            return false;
        }

        $this->_checkAggregationIdInHitForm($hitForm);

        switch ($hitForm->getType()) {
            case 'article':
                $this->_checkArticleIdInHitForm($hitForm);
                break;
            case 'type':
                $this->_checkTopicIdInHitForm($hitForm);
                break;
            case 'tag':
                $this->_checkTagIdInHitForm($hitForm);
                break;
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
     * @param HitForm $hitForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkHitIdInHitForm(HitForm $hitForm): void
    {
        $id = $hitForm->getId();

        if (empty($id)) {
            return;
        }

        $hitVO = $this->_getHitVOFromHitForm($hitForm);

        if (empty($hitVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(sprintf(
                HitForm::HITS_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));
        }
    }

    /**
     * @param HitForm $hitForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkAggregationIdInHitForm(HitForm $hitForm): void
    {
        $id = $hitForm->getId();

        if (empty($id)) {
            return;
        }

        $aggregationVO = $this->_getAggregationVOFromHitForm($hitForm);

        if (empty($aggregationVO)) {
            $hitForm->setStatusFail();

            switch ($hitForm->getAggregationType()) {
                case 'day':
                    $errorMessage = sprintf(
                        HitForm::HITS_AGGREGATION_BY_DAY_NOT_EXISTS_ERROR_MESSAGE,
                        $id
                    );

                    $hitForm->setError($errorMessage);
                    break;
                case 'month':
                    $errorMessage = sprintf(
                        HitForm::HITS_AGGREGATION_BY_MONTH_NOT_EXISTS_ERROR_MESSAGE,
                        $id
                    );

                    $hitForm->setError($errorMessage);
                    break;
                case 'year':
                    $errorMessage = sprintf(
                        HitForm::HITS_AGGREGATION_BY_YEAR_NOT_EXISTS_ERROR_MESSAGE,
                        $id
                    );

                    $hitForm->setError($errorMessage);
                    break;
            }
        }
    }

    /**
     * @param HitForm $hitForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkArticleIdInHitForm(HitForm $hitForm): void
    {
        /* @var $articleModel Article */
        $articleModel = $this->getModel('article');

        $articleVO = $articleModel->getVOById(
            $hitForm->getArticleId(),
            true,
            true
        );

        if (empty($articleVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(sprintf(
                HitForm::ARTICLE_NOT_EXISTS_ERROR_MESSAGE,
                $hitForm->getId()
            ));
        }
    }

    /**
     * @param HitForm $hitForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTopicIdInHitForm(HitForm $hitForm): void
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        $topicVO = $topicModel->getVOById(
            $hitForm->getArticleId(),
            true,
            true
        );

        if (empty($topicVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(sprintf(
                HitForm::TOPIC_NOT_EXISTS_ERROR_MESSAGE,
                $hitForm->getId()
            ));
        }
    }

    /**
     * @param HitForm $hitForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTagIdInHitForm(HitForm $hitForm): void
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        $tagVO = $tagModel->getVOById(
            $hitForm->getArticleId(),
            true,
            true
        );

        if (empty($tagVO)) {
            $hitForm->setStatusFail();

            $hitForm->setError(sprintf(
                HitForm::TAG_NOT_EXISTS_ERROR_MESSAGE,
                $hitForm->getId()
            ));
        }
    }

    /**
     * @param HitForm $hitForm
     * @param bool $isCreateVOIfEmptyId
     * @return HitValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getHitVOFromHitForm(
        HitForm $hitForm,
        bool    $isCreateVOIfEmptyId = false
    ): ?HitValuesObject
    {
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
     * @param HitForm $hitForm
     * @param bool $isCreateVOIfEmptyId
     * @return ModelValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getAggregationVOFromHitForm(
        HitForm $hitForm,
        bool    $isCreateVOIfEmptyId = false
    ): ?ModelValuesObject
    {
        $row = null;

        $id = $hitForm->getId();
        $type = $hitForm->getAggregationType();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getAggregationRowById($type, $id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $aggregationVO = $this->_getAggregationVO($type, $row);

        $aggregationVO->setIsActive($hitForm->isActive());

        return $aggregationVO;
    }

    /**
     * @param string $aggregationType
     * @param array|null $row
     * @return ModelValuesObject|null
     * @throws Exception
     */
    private function _getAggregationVO(
        string $aggregationType,
        ?array $row = null
    ): ?ModelValuesObject
    {
        $aggregationVO = null;

        switch ($aggregationType) {
            case 'day':
                $aggregationVO = new HitAggregationByDayValuesObject($row);
                break;
            case 'month':
                $aggregationVO = new HitAggregationByMonthValuesObject($row);
                break;
            case 'year':
                $aggregationVO = new HitAggregationByYearValuesObject($row);
                break;
        }

        if (empty($aggregationVO)) {
            throw new Exception('Invalid Aggregation Type');
        }

        return $aggregationVO;
    }
}
