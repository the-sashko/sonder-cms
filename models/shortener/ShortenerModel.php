<?php

namespace Sonder\Models;

use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Shortener\Interfaces\IShortenerApi;
use Sonder\Models\Shortener\Interfaces\IShortenerForm;
use Sonder\Models\Shortener\Interfaces\IShortenerModel;
use Sonder\Models\Shortener\Interfaces\IShortenerStore;
use Sonder\Models\Shortener\Forms\ShortenerForm;
use Sonder\Models\Shortener\Interfaces\IShortenerValuesObject;
use Sonder\Models\Shortener\ValuesObjects\ShortenerValuesObject;
use Sonder\Plugins\MathPlugin;
use Throwable;

/**
 * @property IShortenerApi $api
 * @property IShortenerStore $store
 */
#[IModel]
#[IShortenerModel]
final class ShortenerModel extends BaseModel implements IShortenerModel
{
    final protected const ITEMS_ON_PAGE = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IShortenerValuesObject|null
     * @throws ModelException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IShortenerValuesObject {
        $row = $this->store->getShortenerRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $shortenerVO ShortenerValuesObject */
            $shortenerVO = $this->getVO($row);

            return $shortenerVO;
        }

        return null;
    }

    /**
     * @param string|null $code
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IShortenerValuesObject|null
     * @throws ModelException
     */
    final public function getVOByCode(
        ?string $code = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IShortenerValuesObject {
        $row = $this->store->getShortenerRowByCode(
            $code,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $shortenerVO ShortenerValuesObject */
            $shortenerVO = $this->getVO($row);

            return $shortenerVO;
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
    final public function getShortenerVOsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getShortenerRowsByPage(
            $page,
            ShortenerModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getShortLinksPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getShortenerRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / ShortenerModel::ITEMS_ON_PAGE);

        if ($pageCount * ShortenerModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeShortenerById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteShortenerById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreShortenerById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreShortenerById($id);
    }

    /**
     * @param IShortenerForm $shortenerForm
     * @return bool
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function save(IShortenerForm $shortenerForm): bool
    {
        $shortenerForm->checkInputValues();

        if (!$shortenerForm->getStatus()) {
            return false;
        }

        $this->_checkIdInShortenerForm($shortenerForm);
        $this->_checkUrlInShortenerForm($shortenerForm);

        if (!$shortenerForm->getStatus()) {
            return false;
        }

        $shortenerVO = $this->_getVOFromShortenerForm($shortenerForm, true);

        $this->store->start();

        try {
            if (!$this->store->insertOrUpdateShortener($shortenerVO)) {
                $this->store->rollback();
                $shortenerForm->setStatusFail();

                return false;
            }

            $row = $this->store->getShortenerRowByUrl($shortenerForm->getUrl());

            if (empty($row)) {
                $this->store->rollback();
                $shortenerForm->setStatusFail();

                return false;
            }

            /* @var $shortenerVO ShortenerValuesObject */
            $shortenerVO = $this->getVO($row);

            $shortenerForm->setId($shortenerVO->getId());
            $shortenerForm->setCode($shortenerVO->getCode());
        } catch (Throwable $thr) {
            $this->store->rollback();

            $shortenerForm->setStatusFail();
            $shortenerForm->setError($thr->getMessage());

            return false;
        }

        $this->store->commit();

        return true;
    }

    /**
     * @param IShortenerForm $shortenerForm
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _checkIdInShortenerForm(
        IShortenerForm $shortenerForm
    ): void {
        $id = $shortenerForm->getId();

        if (empty($id)) {
            return;
        }

        $shortenerVO = $this->_getVOFromShortenerForm($shortenerForm);

        if (empty($shortenerVO)) {
            $shortenerForm->setStatusFail();

            $shortenerForm->setError(
                sprintf(
                    ShortenerForm::SHORTENER_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param IShortenerForm $shortenerForm
     * @return void
     * @throws ValuesObjectException
     */
    private function _checkUrlInShortenerForm(
        IShortenerForm $shortenerForm
    ): void {
        $row = $this->store->getShortenerRowByUrl($shortenerForm->getUrl());

        if (!empty($row)) {
            $shortenerVO = new ShortenerValuesObject($row);

            $shortenerForm->setStatusFail();

            $shortenerForm->setError(
                sprintf(
                    ShortenerForm::SHORTENER_ALREADY_EXISTS_ERROR_MESSAGE,
                    $shortenerForm->getUrl(),
                    $shortenerVO->getCode()
                )
            );
        }
    }

    /**
     * @param IShortenerForm $shortenerForm
     * @param bool $isCreateVOIfEmptyId
     * @return IShortenerValuesObject|null
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromShortenerForm(
        IShortenerForm $shortenerForm,
        bool $isCreateVOIfEmptyId = false
    ): ?IShortenerValuesObject {
        $row = null;

        $id = $shortenerForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getShortenerRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $shortenerVO = new ShortenerValuesObject($row);

        $shortenerVO->setUrl($shortenerForm->getUrl());
        $shortenerVO->setIsActive($shortenerForm->isActive());

        $this->_setCodeToVO($shortenerVO);

        return $shortenerVO;
    }

    /**
     * @param IShortenerValuesObject $shortenerVO
     * @return void
     * @throws CoreException
     */
    private function _setCodeToVO(IShortenerValuesObject $shortenerVO): void
    {
        if (empty($shortenerVO->getCode())) {
            /* @var MathPlugin MathPlugin */
            $mathPlugin = $this->getPlugin('math');

            $maxId = $this->store->getMaxId();

            $code = $mathPlugin->dec2base64($maxId + 1, 62);

            $shortenerVO->setCode($code);
        }
    }
}
